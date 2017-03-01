<?php

namespace My\Models;

class Content extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageContent($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($arrCondition = array())
    {
        return $this->getParentTable()->getList($arrCondition);
    }

    public function getListLimit($arrCondition, $intPage, $intLimit, $strOrder)
    {
        return $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
    }

    public function getTotal($arrCondition)
    {
        return $this->getParentTable()->getTotal($arrCondition);
    }

    public function getDetail($arrCondition)
    {
        return $this->getParentTable()->getDetail($arrCondition);
    }

    public function add($p_arrParams)
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function edit($p_arrParams, $intContentID)
    {
        return $this->getParentTable()->edit($p_arrParams, $intContentID);
    }

    public function multiEdit($p_arrParams, $arrCondition)
    {
        return $this->getParentTable()->multiEdit($p_arrParams, $arrCondition);
    }

}
