<?php

/**
 * Desc: 发货单结算明细
 */
class DeliverySettlementDetailSub extends BaseHasSubActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_delivery_settlement_detail_sub';
    }

    public function relations() {
        return array(
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "deliveryOrder" => array(self::BELONGS_TO, "DeliveryOrder", "order_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "stockDeliveryDetail" => array(self::HAS_MANY, "StockDeliveryDetail", "detail_id"),
            "stockOutDetail" => array(self::HAS_MANY, "StockOutDetail", "detail_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
            "stockDetail"=>array(self::HAS_MANY, "StockDeliveryDetail", 'detail_id')
        );
    }

    public function beforeSave() {
        if ($this->update_time == $this->getOldAttribute("update_time")) {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }


}