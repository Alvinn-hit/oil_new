<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 18:30
 * Describe：
 */
class ContractSettlement extends BaseActiveRecord
{


    const STATUS_STOP   = -9;//合同结算作废
    const STATUS_RECALL = -2;//合同结算撤回
    const STATUS_BACK   = -1;//合同结算审核驳回
    const STATUS_NEW    = 0; //合同结算新创建
    const STATUS_TEMP_SAVE = 1;//合同结算已暂存
    const STATUS_SAVED  = 2; //合同结算已保存


    const STATUS_SUBMIT = 10; //合同结算已提交
    const STATUS_PASS   = 20; //合同结算审核通过


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract_settlement';
    }

    public function relations() {
        return array(
            "contractSettlementGoods"=>array(self::HAS_MANY, "ContractSettlementGoods", array('settle_id'=>'settle_id', 'contract_id'=>'contract_id')),
            "contractSettlementSubjectDetail"=>array(self::HAS_MANY, "ContractSettlementSubjectDetail", array('settle_id'=>'settle_id', 'contract_id'=>'contract_id')),
            "contract"=>array(self::HAS_ONE, "Contract", array('contract_id'=>'contract_id')),
        );
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

    /**
     * 根据合同id查找对应的信息
     * @param $contractId
     * @param $condition
     * @return CActiveRecord
     */
    public function findByContractId($contractId, $condition = '')
    {
        return parent::find("contract_id=" . $contractId . $condition);
    }
}
