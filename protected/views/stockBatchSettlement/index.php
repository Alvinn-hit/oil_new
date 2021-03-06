<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code', 'text' => '入库通知单编号'),
       array('type' => 'text', 'key' => 'e.contract_code', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
       array('type' => 'text', 'key' => 'b.project_code', 'text' => '项目编号'),
       array('type' => 'text', 'key' => 'c.name*', 'text' => '上游合作方'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '交易主体'),
       array('type' => 'select', 'map_name'=>'stock_notice_delivery_type', 'key' => 'a.type', 'text' => '发货方式'),
       array('type' => 'date', 'id'=>'settleStartTime','key' => 's.settle_date>', 'text' => '结算日期'),
       array('type' => 'date','id'=>'settleEndTime', 'key' => 's.settle_date<', 'text' => '到'),
       array('type' => 'select', 'map_name'=>'delivery_settlement_status', 'key' => 'status', 'text' => '状态&emsp;&emsp;&emsp;'),
       
   )
);

//列表显示
$array = array(
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'batch_id,code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '入库通知单编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/stockIn/detail/?id={1}&t=1"  target="_blank">{2}</a>' ),
    array('key' => 'settle_date', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '结算日期'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a id="t_{1}" title="{2}" target="_blank" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text' => '<a id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1" target="_blank">{2}</a>'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'stock_notice_delivery_type', 'style' => 'width:60px;text-align:center', 'text' => '发货方式'),
    array('key' => 'status_desc', 'type' => 'map_val', 'map_name' => 'delivery_settlement_status', 'style' => 'width:80px;text-align:center', 'text' => '状态'),

);

function getRowActions($row, $self) {
    $links = array();
    /* if($row['status']<StockNotice::STATUS_SETTLE_SUBMIT) {
        if(StockBatchSettlementService::checkIsCanEdit($row["batch_id"])) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["batch_id"] . '" title="修改">修改</a>';
        } else {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["batch_id"] . '" title="结算">结算</a>';
        }
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["batch_id"] . '" title="查看">查看</a>'; */
    if(empty($row['settle_id']))
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["batch_id"] . '" title="结算">结算</a>';
    else{
        if($row['settle_status']!=\ddd\domain\entity\contractSettlement\SettlementStatus::STATUS_NEW){
            if(\ddd\application\contractSettlement\SettleService::settlementIsCanEdit($row["settle_status"]))
                $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["batch_id"] . '" title="修改">修改</a>';
            if(!empty($row['settle_type'])){
                if($row['settle_type']==\ddd\domain\entity\contractSettlement\SettlementMode::LADING_BILL_MODE_SETTLEMENT)
                    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["batch_id"] . '" title="查看">查看</a>';
                else //待修改
                    $links[] = '<a href="/buyContractSettlement/detail?id=' . $row["contract_id"] . '" title="查看">查看</a>';
            }
        }else{
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["batch_id"] . '" title="结算">结算</a>';
        }
        
    }
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");