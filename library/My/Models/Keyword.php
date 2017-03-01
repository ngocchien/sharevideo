<?php

namespace My\Models;

class Keyword extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageKeyword($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function add($p_arrParams)
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function getListLimit($arrCondition = [], $intPage = 1, $intLimit = 15, $strOrder = 'key_id ASC')
    {
        $arrResult = $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
        return $arrResult;
    }

    public function edit($p_arrParams, $id)
    {
        $intResult = $this->getParentTable()->edit($p_arrParams, $id);
        return $intResult;
    }

}
