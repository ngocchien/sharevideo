<?php

namespace My\Models;

class Tags extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageTags($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($arrCondition = array())
    {
        return $this->getParentTable()->getList($arrCondition);
    }

    public function getListLimit($arrCondition = array(), $intPage = 1, $intLimit = 15, $strOrder = 'tags_id DESC')
    {
        return $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
    }

    public function getTotal($arrCondition)
    {
        return $this->getParentTable()->getTotal($arrCondition);
    }

    public function add($p_arrParams)
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function edit($p_arrParams, $intTagID)
    {
        return $this->getParentTable()->edit($p_arrParams, $intTagID);
    }

    public function getDetail($arrCondition = array())
    {
        return $this->getParentTable()->getDetail($arrCondition);
    }

}
