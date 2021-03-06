<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/16 0016
 * Time: 9:56
 */

namespace ddd\repository\payment;


use ddd\domain\entity\payment\PayClaim;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\ExceptionService;
use ddd\Common\Repository\EntityRepository;

class PayClaimRepository extends EntityRepository
{
    public function init()
    {
        $this->with = array("apply");
    }

    public function getActiveRecordClassName()
    {
        return "PayClaim";
    }

    public function getNewEntity()
    {
        return new PayClaim();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = PayClaim::create();
        $entity->setAttributes($model->getAttributes(), false);

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {

    }
}