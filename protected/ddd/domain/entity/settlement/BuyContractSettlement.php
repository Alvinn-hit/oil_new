<?php
/**
 * Created by vector.
 * DateTime: 2018/3/21 11:35
 * Describe：采购合同结算单
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\Quantity;
use ddd\domain\event\contractSettlement\BuyContractSettlementEvent;
use ddd\domain\event\contractSettlement\BuyContractSettlementRejectEvent;
use ddd\domain\event\contractSettlement\BuyContractSettlementSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\iRepository\contractSettlement\IBuyContractSettlementRepository;
use ddd\domain\service\contract\ContractService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\repository\contractSettlement\BuyContractSettlementRepository;
use ddd\repository\contractSettlement\LadingBillSettlementRepository;
use ddd\repository\stock\LadingBillRepository;

class BuyContractSettlement extends Settlement
{
    /**
     * 提单结算单信息
     *        array(LadingBillSettlement)
     * @var      array
     */
    public $bill_settlements;

    /**
     * 结算类型
     * @var int
     */
    public $settle_type;

    /**
     * @var IBuyContractSettlementRepository
     */
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init(); // TODO: Change the autogenerated stub

    }

    /**
     * 获取仓储
     * @return IBuyContractSettlementRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(IBuyContractSettlementRepository::class);
        }
        return $this->repository;
    }

    
    /**
     * 创建采购合同结算单对象
     * @param Contract $contract
     * @return BuyContractSettlement
     * @throws \Exception
     */
    public static function create(Contract $contract)
    {
        if(empty($contract))
            throw new ZException("参数Contract对象为空");

        $isBoolAndMsg = ContractService::service()->isCanSettle($contract);
        if($isBoolAndMsg!==true)
            throw new ZException($isBoolAndMsg);

        if($contract->settle_type==SettlementMode::LADING_BILL_MODE_SETTLEMENT){
            $entity = BuyContractSettlementRepository::repository()->findContractSettlement($contract->contract_id);
            if(empty($entity))
                throw new ZException("BuyContractSettlement对象不存在");
            
            $settlements = LadingBillSettlementRepository::repository()->findAllByContractId($contract->contract_id);
            if(empty($settlements))
                throw new ZException("LadingBillSettlement对象不存在");

            $entity->bill_settlements = $settlements;
        }else{
            $entity=new BuyContractSettlement();
            $entity->contract_id=$contract->contract_id;
            $entity->settle_currency=Currency::getCurrency($contract->currency);
            $entity->status=SettlementStatus::STATUS_NEW;

            $goodsItems = array();
            $ladingBills = LadingBillRepository::repository()->findAllByContractId($contract->contract_id);
            if(is_array($ladingBills) && !empty($ladingBills)) {
                foreach ($ladingBills as $lading)
                {
                    if(is_array($lading->items) && !empty($lading->items)){
                      foreach ($lading->items as $item) {
                        $goodsItems[$item->goods_id][$lading->id] = $item;
                      }
                    }
                }
            }

            if(!empty($goodsItems)){
                $flag = false;
                foreach ($goodsItems as $goodsId => $ladingGoods) {
                    $in_quantity     = 0;
                    $unit            = 0;
                    $in_quantity_sub = 0;
                    $unit_sub        = 0;
                    $item=GoodsSettlement::create($goodsId);
                    foreach ($ladingGoods as $k => $v) {
                        $in_quantity     += $v->in_quantity->quantity;
                        $unit            = $v->in_quantity->unit;
                        $in_quantity_sub += $v->in_quantity_sub->quantity;
                        $unit_sub        = $v->in_quantity_sub->unit;

                        $flag =  (!empty($unit_sub) && $unit!=$unit_sub);


                        $billItem = BillSettlementItem::create($goodsId);
                        $billItem->bill_id = $k;
                        $billItem->bill_quantity   = $v->in_quantity;
                        $billItem->settle_quantity = $v->in_quantity;
                        $billItem->loss_quantity   = new Quantity(0, $v->in_quantity->unit);

                        if($flag){
                            $billItem->bill_quantity_sub   = $v->in_quantity_sub;
                            $billItem->settle_quantity_sub = $v->in_quantity_sub;
                            $billItem->loss_quantity_sub   = new Quantity(0, $v->in_quantity_sub->unit);
                        }

                        $item->addBillSettlementItem($billItem);
                    }

                    $item->bill_quantity   = new Quantity($in_quantity, $unit);
                    $item->settle_quantity = new Quantity($in_quantity, $unit);
                    $item->loss_quantity   = new Quantity(0, $unit);
                    if($flag){
                        $item->bill_quantity_sub   = new Quantity($in_quantity_sub, $unit_sub);
                        $item->settle_quantity_sub = new Quantity($in_quantity_sub, $unit_sub);
                        $item->loss_quantity_sub   = new Quantity(0, $unit_sub);
                    }

                    $entity->addGoodsSettlement($item);
                }
            }
        }
        
        
        $other=OtherSettlement::create();
        $entity->addOtherSettlement($other);
  
        return $entity;
    }


    /**
     * 增加货款金额
     * @param    int $amount
     */
    public function addAndSaveGoodsAmount($amount)
    {
        $this->goods_amount+=$amount;
        $this->repository->addAndSaveGoodsAmount($this,$amount);
    }

    /**
     * 采购合同结算单提交
     * @throws \CException
     */
    public function submit()
    {
        $this->status = SettlementStatus::STATUS_SUBMIT;
        $this->repository->submit($this);

        $this->afterSubmit();
    }

    /**
     * 采购合同结算单提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::BuyContractSettlementSubmitEvent);
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new BuyContractSettlementSubmitEvent($this));
    }

    /**
     * 响应采购合同结算单提交事件
     * @param  $event
     * @throws \CException 
     */
    public function onAfterSubmit($event)
    {
        $this->raiseEvent('onAfterSubmit', $event);
    }


    public function checkPass()
    {
        $this->status = SettlementStatus::STATUS_PASS;
        $this->repository->setSettled($this);

        $this->afterCheckPass();
    }

    public function afterCheckPass()
    {
        EventSubscribeService::bind($this,"onAfterCheckPass", EventSubscribeService::BuyContractSettlementPassEvent);
        if($this->hasEventHandler('onAfterCheckPass'))
            $this->onAfterCheckPass(new BuyContractSettlementEvent($this));
    }

    public function onAfterCheckPass($event)
    {
        $this->raiseEvent('onAfterCheckPass', $event);
    }


    public function checkBack()
    {
        $this->status = SettlementStatus::STATUS_BACK;
        $this->repository->back($this);

        $this->afterCheckBack();
    }

    public function afterCheckBack()
    {
        EventSubscribeService::bind($this,"onAfterCheckBack", EventSubscribeService::BuyContractSettlementBackEvent);
        if($this->hasEventHandler('onAfterCheckBack'))
            $this->onAfterCheckBack(new BuyContractSettlementRejectEvent($this));
    }

    public function onAfterCheckBack($event)
    {
        $this->raiseEvent('onAfterCheckBack', $event);
    }


    /**
     * 生成编号
     */
    public function generateId()
    {
         $this->settle_id=\IDService::getContractSettlementId();
    }

    /**
     * 生成编码
     */
    public function generateCode()
    {
        $this->code=\IDService::getContractSettlementCode();
    }




}