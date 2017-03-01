<?php

namespace My\Models;

class Contact extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageContact($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($arrCondition = array())
    {
        return $this->getParentTable()->getList($arrCondition);
    }

    public function getListLimit($arrCondition = array(), $intPage = 1, $intLimit = 15, $strOrder = 'created_date ASC')
    {
        return $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
    }

    public function getTotal($arrCondition = array())
    {
        return $this->getParentTable()->getTotal($arrCondition);
    }

    public function getDetail($arrCondition = array())
    {
        return $this->getParentTable()->getDetail($arrCondition);
    }

    public function add($p_arrParams = array())
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function edit($p_arrParams, $id)
    {
        return $this->getParentTable()->edit($p_arrParams, $id);
    }

}

