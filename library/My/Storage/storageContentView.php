<?php

namespace My\Storage;

use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Sql\Sql,
    Zend\Db\Adapter\Adapter;

class storageContentView extends AbstractTableGateway
{

    protected $table = 'tbl_content_view';

    public function __construct(Adapter $adapter)
    {
        $adapter->getDriver()->getConnection()->connect();
        $this->adapter = $adapter;
    }

    public function __destruct()
    {
        $this->adapter->getDriver()->getConnection()->disconnect();
    }

    public function getList($arrCondition = array())
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)
                ->where('1=1' . $strWhere)
                ->order(array('id ASC', 'id ASC'));
            $query = $sql->getSqlStringForSqlObject($select);
            return $adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray();
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMesseges());
                echo '</pre>';
                die();
            }
            return array();
        }
    }

    public function getListLimit($arrCondition, $intPage, $intLimit, $strOrder)
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)
                ->where('1=1' . $strWhere)
                ->order($strOrder)
                ->limit($intLimit)
                ->offset($intLimit * ($intPage - 1));
            $query = $sql->getSqlStringForSqlObject($select);
            return $adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray();
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            return array();
        }
    }

    public function getTotal($arrCondition = [])
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)
                ->columns(array('total' => new \Zend\Db\Sql\Expression('COUNT(*)')))
                ->where('1=1' . $strWhere);
            $query = $sql->getSqlStringForSqlObject($select);
            return (int)current($adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray())['total'];
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            return false;
        }
    }

    public function getDetail($arrCondition = array())
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)
                ->where('1=1' . $strWhere);
            $query = $sql->getSqlStringForSqlObject($select);
            return current($adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray());
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                die($exc->getMessage());
            }
            return array();
        }
    }

    public function add($p_arrParams)
    {
        try {
            if (!is_array($p_arrParams) || empty($p_arrParams)) {
                return false;
            }
            $result = $this->insert($p_arrParams);
            if ($result) {
                $result = $this->lastInsertValue;
                $p_arrParams['id'] = $result;
                $instanceJob = new \My\Job\JobContentView();
                $instanceJob->addJob(SEARCH_PREFIX . 'writeContentView', $p_arrParams);
            }
            return $result;
        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            return false;
        }
    }

    public function edit($p_arrParams, $id)
    {
        try {
            if (!is_array($p_arrParams) || empty($p_arrParams) || empty($id)) {
                return false;
            }
            $result = $this->update($p_arrParams, 'id=' . $id);
            if ($result) {
                $p_arrParams['id'] = $id;
                $instanceJob = new \My\Job\JobContentView();
                $instanceJob->addJob(SEARCH_PREFIX . 'editContentView', $p_arrParams);
            }
            return $result;
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                die();
            }
            return false;
        }
    }

    private function _buildWhere($arrCondition)
    {

        $strWhere = '';

        if (isset($arrCondition['id'])) {
            $strWhere .= " AND id=" . $arrCondition['id'];
        }

        if (isset($arrCondition['cont_id'])) {
            $strWhere .= " AND cont_id=" . $arrCondition['cont_id'];
        }

        if (!empty($arrCondition['created_date'])) {
            $strWhere .= " AND created_date = '" . $arrCondition['created_date'] . "'";
        }

        return $strWhere;
    }

}
