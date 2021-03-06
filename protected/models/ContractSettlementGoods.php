<?php
/**
 * Created by vector.
 * DateTime: 2018/3/27 11:35
 * Describe：合同结算单商品结算明细
 */

class ContractSettlementGoods extends BaseHasSubActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract_settlement_goods';
    }

    public function relations() {
        return array(
            "ladings"=>array(self::HAS_MANY, "StockBatchSettlement", array('settle_id'=>'settle_id',"goods_id"=>'goods_id','contract_id'=>'contract_id')),
            "orders"=>array(self::HAS_MANY, "DeliverySettlementDetail", array('settle_id'=>'settle_id',"goods_id"=>'goods_id','contract_id'=>'contract_id')),
            "ladingSettlement" => array(self::HAS_ONE, "StockBatchSettlement", "item_id"),
            "orderSettlement"  => array(self::HAS_ONE, "DeliverySettlementDetail", "item_id"),
            "settleSub" => array(self::HAS_ONE, "ContractSettlementGoodsSub", "item_id"),
            "settleGoods"=>array(self::HAS_ONE, "ContractSettlementGoodsDetail", array('item_id'=>'item_id')),
            "fees"=>array(self::HAS_MANY, "ContractSettlementGoodsDetailItem", array('item_id'=>'item_id')),
            "ladingAttachments" =>array(self::HAS_MANY, "StockBatchSettlementAttachment", array("base_id" => "item_id"), "on" => "ladingAttachments.status=1"),
            "deliveryAttachments" =>array(self::HAS_MANY, "DeliverySettlementAttachment", array("base_id" => "item_id"), "on" => "deliveryAttachments.status=1"),
            "goodsAttachments" =>array(self::HAS_MANY, "ContractSettlementAttachment", array("base_id" => "item_id"), "on" => "goodsAttachments.status=1"),
            );
    }


    public function __set($name, $value)
    {
        $relations= $this->relations();
        if(isset($relations["settleSub"]) && $this->checkIsSub($name))
        {
            $newName=$this->getSubName($name);
            if(empty($this->settleSub))
            {
                $this->settleSub=new $relations["settleSub"][1];
            }
            $this->settleSub[$newName]=$value;
        }
        else
            parent::__set($name, $value); // TODO: Change the autogenerated stub
    }

    protected function beforeFind()
    {
        $relations= $this->relations();
        if(isset($relations["settleSub"]))
            $this->with("settleSub");
        parent::beforeFind(); // TODO: Change the autogenerated stub
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    protected function afterSave()
    {
        if(!empty($this->settleSub) && !empty($this->settleSub["unit"]) && $this->unit!=$this->settleSub["unit"])
        {
            $res=true;
            if($this->settleSub->isNewRecord)
            {
                $this->settleSub->setPrimaryKey($this->getPrimaryKey());
                $this->settleSub->update_user_id = $this->update_user_id;
                $this->settleSub->update_time = $this->update_time;
                $res=$this->settleSub->save();
            }
            else
            {
                $this->settleSub->setDiffAttributes();
                $diff= $this->settleSub->getDiffAttributes();
                if(is_array($diff) && count($diff)>0)
                    $res=$this->settleSub->save();
            }
            if(!$res)
            {
                throw new Exception("保存SettleSub信息出错");
            }
        }

        parent::afterSave(); // TODO: Change the autogenerated stub
    }
}