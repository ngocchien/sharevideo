<?php

namespace My\Models;

class Group extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageGroup($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($arrCondition = array())
    {
        return $this->getParentTable()->getList($arrCondition);
    }

    public function getListLimit($arrCondition = array(), $intPage = 1, $intLimit = 15, $strOrder = 'group_id DESC')
    {
        return $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
    }

    public function getDetail($arrCondition = array())
    {
        return $this->getParentTable()->getDetail($arrCondition);
    }

    public function getTotal($arrCondition = array())
    {
        return $this->getParentTable()->getTotal($arrCondition);
    }

    public function add($p_arrParams)
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function edit($p_arrParams, $groupId)
    {
        return $this->getParentTable()->edit($p_arrParams, $groupId);
    }
}
