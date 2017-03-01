<?php

namespace My\Models;

class Permission extends ModelAbstract
{

    private function getParentTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new \My\Storage\storagePermission($dbAdapter);
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getList($arrCondition = array())
    {
        return $this->getParentTable()->getList($arrCondition);
    }

    public function getListjoinRole($arrCondition = array())
    {
        return $this->getParentTable()->getListjoinRole($arrCondition);
    }

    public function getListLimit($arrCondition = array(), $intPage = 1, $intLimit = 15, $strOrder = 'permission_id DESC')
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

    public function addAll($p_arrParams)
    {
        return $this->getParentTable()->addAll($p_arrParams);
    }

    public function edit($p_arrParams, $intPermissionID)
    {
        return $this->getParentTable()->edit($p_arrParams, $intPermissionID);
    }

    public function editBy($p_arrParams, $strParams)
    {
        return $this->getParentTable()->editBy($p_arrParams, $strParams);
    }

    public function remove($arrCondition)
    {
        return $this->getParentTable()->remove($arrCondition);
    }

    public function getAllResource()
    {
        $dirScanner = new \Zend\Code\Scanner\DirectoryScanner();
        $dirScanner->addDirectory(WEB_ROOT . '/module/Backend/src/Backend/Controller/');
        foreach ($dirScanner->getClasses(true) as $classScanner) {
            list($moduleName, $tmp, $controllerName) = explode('\\', $classScanner->getName());
            $controllerName = str_replace('Controller', '', $controllerName);
            $action = array();
            foreach ($classScanner->getMethods(true) as $method) {
                if (strpos($method->getName(), 'Action')) {
                    $action[] = str_replace('Action', '', $method->getName());
                }
            }
            $arrData[] = array('module' => $moduleName, 'controller' => $controllerName, 'action' => $action);
        }
        return $arrData;
    }

}
