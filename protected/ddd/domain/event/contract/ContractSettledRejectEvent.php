<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/23
 * Time: 20:02
 */

namespace ddd\domain\event\contract;


use ddd\Common\Domain\BaseEvent;

class ContractSettledRejectEvent extends BaseEvent
{
    function initEventName()
    {
        parent::initEventName(); // TODO: Change the autogenerated stub
        $this->eventName = '合同结算审核驳回';
    }
}