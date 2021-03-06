<?php
/**
 * Created by vector.
 * DateTime: 2018/3/21 11:35
 * Describe：销售合同结算单
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\Quantity;
use ddd\domain\event\contractSettlement\SaleContractSettlementEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementRejectEvent;
use ddd\domain\event\contractSettlement\SaleContractSettlementSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\domain\iRepository\contractSettlement\ISaleContractSettlementRepository;
use ddd\domain\service\contract\ContractService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\repository\contractSettlement\DeliveryOrderSettlementRepository;
use ddd\repository\contractSettlement\SaleContractSettlementRepository;
use ddd\repository\stock\DeliveryOrderRepository;

class SaleContractSettlement extends Settlement
{
    /**
     * 发货单结算单信息
     *        array(DeliveryOrderSettlement)
     * @var      array
     */
    public $bill_settlements;

    /**
     * 结算类型
     * @var int
     */
    public $settle_type;

    /**
     * @var ISaleContractSettlementRepository
     */
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init(); // TODO: Change the autogenerated stub

    }

    /**
     * 获取仓储
     * @return ISaleContractSettlementRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(ISaleContractSettlementRepository::class);
        }
        return $this->repository;
    }

    
    /**
     * 创建销售合同结算单对象
     * @param Contract $contract
     * @return BuyContractSettlement
     * @throws \Exception
     */
    public static function create(Contract $contract)
    {
        if(empty($contract))
            throw new ZException("Contract对象不存在");

        $isBoolAndMsg = ContractService::service()->isCanSettle($contract);
        if($isBoolAndMsg !== true)
            throw new ZException($isBoolAndMsg);

        if($contract->settle_type == SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
            $entity = SaleContractSettlementRepository::repository()->findContractSettlement($contract->contract_id);
            if(empty($entity))
                throw new ZException("SaleContractSettlement对象不存在");

            $settlements = DeliveryOrderSettlementRepository::repository()->findAllByContractId($contract->contract_id);
            if(empty($settlements))
                throw new ZException("DeliveryOrderSettlement对象不存在");

            $entity->bill_settlements = $settlements;
        }else{
            $entity = new SaleContractSettlement();
            $entity->contract_id     =  $contract->contract_id;
            $entity->settle_currency =  Currency::getCurrency($contract->currency);
            $entity->status          =  SettlementStatus::STATUS_NEW;

            $goodsItems = array();
            $deliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($contract->contract_id);
            if(is_array($deliveryOrders) && !empty($deliveryOrders)) {
                foreach ($deliveryOrders as $order)
                {
                    if(is_array($order->items) && !empty($order->items)){
                        foreach ($order->items as $item) {
                            $goodsItems[$item->goods_id][$order->order_id] = $item;
                        }
                    }
                }
            }



            if(!empty($goodsItems)){
                foreach ($goodsItems as $goodsId => $orderGoods) {
                    $out_quantity = 0;
                    $unit = 0;
                    $item = GoodsSettlement::create($goodsId);
                    foreach ($orderGoods as $k => $v) {
                        $out_quantity += $v->out_quantity->quantity;
                        $unit          = $v->out_quantity->unit;

                        $billItem = BillSettlementItem::create($goodsId);
                        $billItem->bill_id         = $k;
                        $billItem->bill_quantity   = $v->out_quantity;
                        $billItem->settle_quantity = $v->out_quantity;
                        $billItem->loss_quantity   = new Quantity(0, $v->out_quantity->unit);

                        $item->addBillSettlementItem($billItem);
                    }

                    $item->bill_quantity   = new Quantity($out_quantity, $unit);
                    $item->settle_quantity = new Quantity($out_quantity, $unit);
                    $item->loss_quantity   = new Quantity(0, $unit);

                    $entity->addGoodsSettlement($item);
                }
            }
        }


        $other = OtherSettlement::create();
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
     * 销售合同结算单提交
     * @throws \CException
     */
    public function submit()
    {
        $this->status = SettlementStatus::STATUS_SUBMIT;
        $this->repository->submit($this);

        $this->afterSubmit();
    }

    /**
     * 销售合同结算单提交后
     * @throws \CException
     */
    public function afterSubmit()
    {
        EventSubscribeService::bind($this,"onAfterSubmit", EventSubscribeService::SaleContractSettlementSubmitEvent);
        if($this->hasEventHandler('onAfterSubmit'))
            $this->onAfterSubmit(new SaleContractSettlementSubmitEvent($this));
    }

    /**
     * 响应销售合同结算单提交事件
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
        EventSubscribeService::bind($this,"onAfterCheckPass", EventSubscribeService::SaleContractSettlementPassEvent);
        if($this->hasEventHandler('onAfterCheckPass'))
            $this->onAfterCheckPass(new SaleContractSettlementEvent($this));
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
        EventSubscribeService::bind($this,"onAfterCheckBack", EventSubscribeService::SaleContractSettlementBackEvent);
        if($this->hasEventHandler('onAfterCheckBack'))
            $this->onAfterCheckBack(new SaleContractSettlementRejectEvent($this));
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