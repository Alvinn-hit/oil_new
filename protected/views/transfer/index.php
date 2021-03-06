<?php
/**
 * Created by vector.
 * DateTime: 2017/10/17 18:16
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    $status = $row["return_cross_status"];//CrossOrderService::isOrderCanAddOrEdit($row['cross_id']);
    if(empty($status) || $status >= CrossOrder::STATUS_PASS)
    {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["cross_id"] . '&contract_id='. $row['buy_id'] .'" title="添加调货">添加</a>';
    }else if($status==CrossOrder::STATUS_BACK || $status == CrossOrder::STATUS_SAVED){
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["cross_id"] . '&contract_id='. $row['buy_id'] . '" title="修改调货">修改</a>';
    }

    /*if (!empty($status)) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["cross_id"] . '&contract_id='. $row['buy_id'] . '" title="查看详情">详情</a>';
    }*/
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["cross_id"] . '&contract_id='. $row['buy_id'] . '" title="查看详情">详情</a>';
    $s = !empty($links) ? implode("&nbsp;|&nbsp;", $links) : '';
    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'o.cross_code*', 'text' => '调货单编号'),
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '销售合同编号&emsp;&emsp;'),
        array('type' => 'text', 'key' => 'scf.code_out*', 'text' => '销售外部合同编号'),
        array('type' => 'text', 'key' => 'pa.name*', 'text' => '下游合作方'),
        array('type' => 'text', 'key' => 'g.name*', 'text' => '品名&emsp;&emsp;&emsp;'),
        array('type' => 'text', 'key' => 'ct.contract_code*', 'text' => '被调采购合同编号'),
        array('type' => 'text', 'key' => 'bcf.code_out*', 'text' => '被调采购外部合同编号'),
    )
);

//列表显示
$array = array(
    array('key' => 'cross_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'cross_code', 'type' => '', 'style' => 'width:220px;text-align:left', 'text' => '调货单编号',),
    array('key' => 'sell_id,sell_code', 'type' => 'href', 'style' => 'width:180px;text-align:left', 'text' => '销售合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'sell_code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '销售外部合同编号'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:110px;text-align:left', 'text' => '品名'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'buy_id,buy_code', 'type' => 'href', 'style' => 'width:180px;text-align:left', 'text' => '被调采购合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'buy_code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '被调采购外部合同编号'),
);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
?>
