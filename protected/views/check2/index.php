<?php
$form_array = array(
	'form_url' => '/'.$this->getId().'/',
	'input_array' => array(
		array('type' => 'text', 'key' => 'c.project_code', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'co.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'u.name', 'text' => '项目负责人'),
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type'=>'select','key'=>'checkStatus','noAll'=>'1','map_name'=>'risk_management_status','text'=>'审核状态'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'b.create_time>','text'=>'申请时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'b.create_time<','text'=>'到')

    )
);
$array = array(
	array('key' => 'project_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowEditAction'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_status', 'type' => 'map_val', 'map_name' => 'contract_status', 'style' => 'width:100px;text-align:center', 'text' => '合同状态'),
    array('key' => 'buy_sell_desc', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '购销信息'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '上游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'project_type_desc', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '项目类型'),
    array('key' => 'name', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '项目负责人'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:100px;text-align:center', 'text' => '创建人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '申请时间'),
    array('key' => 'checkStatus', 'type' => 'map_val', 'map_name' => 'risk_management_status', 'style' => 'width:80px;text-align:center', 'text' => '审核状态'),
);

function getRowEditAction($row, $self) {
	// 状态待确认
	if($row['contract_status'] == Contract::STATUS_SUBMIT) {
	} else {
	}
	if($row['checkStatus'] == 1) {
		$html = "<a href='/{$self->getId()}/check?id=" . $row["obj_id"] . "' title='审核'>审核</a>";
	} else {
		$html = "<a href='/{$self->getId()}/detail?detail_id=" . $row['detail_id'] . "' title='查看'>查看</a>";
	}
	return $html;
}

/*function getUpPartnerName($row) {
    $up_partner_id = 0;
    $up_partner_name = '';
    if($row['contract_type'] == ConstantMap::BUY_TYPE) {
        $up_partner_id = $row['up_partner_id'];
        $up_partner_name = $row['up_partner_name'];
    }else{
        if($row['is_main'] == 1 && !empty($row['partner_id']) && !empty($row['partner_name'])) {
            $up_partner_id = $row['partner_id'];
            $up_partner_name = $row['partner_name'];
        }
    }
    return '<a target="_blank" title="'.$up_partner_name.'" href="/partner/detail/?id='.$up_partner_id.'&t=1" >'.$up_partner_name.'</a>';
}

function getDownPartnerName($row) {
    $down_partner_id = 0;
    $down_partner_name = '';
    if($row['contract_type'] == ConstantMap::SALE_TYPE) {
        $down_partner_id = $row['down_partner_id'];
        $down_partner_name = $row['down_partner_name'];
    }else{
        if($row['is_main'] == 1 && !empty($row['partner_id']) && !empty($row['partner_name'])) {
            $down_partner_id = $row['partner_id'];
            $down_partner_name = $row['partner_name'];
        }
    }
    return '<a target="_blank" title="'.$down_partner_name.'" href="/partner/detail/?id='.$down_partner_id.'&t=1" >'.$down_partner_name.'</a>';
}*/

$this->loadForm($form_array, $_data_);
$this->show_table($array,$_data_['data'],"","min-width:1650px;","table-bordered table-layout data-table","",true);

