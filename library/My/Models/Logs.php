<?php

namespace My\Models;

class Logs extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storageLogs($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function add($p_arrParams)
    {
        return $this->getParentTable()->add($p_arrParams);
    }

    public function getListLimit($arrCondition = [], $intPage = 1, $intLimit = 15, $strOrder = 'log_id ASC')
    {
        $arrResult = $this->getParentTable()->getListLimit($arrCondition, $intPage, $intLimit, $strOrder);
        return $arrResult;
    }

}
