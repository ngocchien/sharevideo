<?php

namespace My\Storage;

use Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\Sql\Sql;

class storageUser extends AbstractTableGateway
{

    protected $table = 'tbl_users';
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $adapter->getDriver()->getConnection()->connect();
        $this->adapter = $adapter;
    }

    public function __destruct()
    {
        $this->adapter->getDriver()->getConnection()->disconnect();
    }

    public function getList($arrCondition = null)
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)
                ->where('1=1' . $strWhere)
                ->order(array('user_id DESC'));
            $query = $sql->getSqlStringForSqlObject($select);
            return $adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray();
        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMessage());
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
            $select = $sql->Select($this->table);
            $select->where('1=1' . $strWhere)
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

    public function getTotal($arrCondition)
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

    public function getDetail($arrCondition)
    {
        try {
            $strWhere = $this->_buildWhere($arrCondition);
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->Select($this->table)->where('1=1' . $strWhere);
            $query = $sql->getSqlStringForSqlObject($select);
            return current($adapter->query($query, $adapter::QUERY_MODE_EXECUTE)->toArray());
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

    public function add($p_arrParams)
    {
        try {
            if (!is_array($p_arrParams) || empty($p_arrParams)) {
                return false;
            }
            $result = $this->insert($p_arrParams);

            if ($result) {
                $result = $this->lastInsertValue;
                $p_arrParams['user_id'] = $result;
                $instanceJob = new \My\Job\JobUser();
                $instanceJob->addJob(SEARCH_PREFIX . 'writeUser', $p_arrParams);
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

    public function edit($p_arrParams, $intUserID)
    {
        if (!is_array($p_arrParams) || empty($p_arrParams) || empty($intUserID)) {
            return false;
        }
        try {
            if (!is_array($p_arrParams) || empty($p_arrParams) || empty($intUserID)) {
                return false;
            }
            $result = $this->update($p_arrParams, 'user_id=' . $intUserID);
            if ($result) {
                $p_arrParams['user_id'] = $intUserID;
                $instanceJob = new \My\Job\JobUser();
                $instanceJob->addJob(SEARCH_PREFIX . 'editUser', $p_arrParams);
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

    private function _buildWhere($arrCondition)
    {
        $strWhere = null;
        if (empty($arrCondition)) {
            return $strWhere;
        }

        if ($arrCondition['user_id'] !== '' && $arrCondition['user_id'] !== NULL) {
            $strWhere .= " AND user_id=" . $arrCondition['user_id'];
        }

        if ($arrCondition['not_user_id'] !== '' && $arrCondition['not_user_id'] !== NULL) {
            $strWhere .= " AND user_id !=" . $arrCondition['not_user_id'];
        }

        if ($arrCondition['user_name'] !== '' && $arrCondition['user_name'] !== NULL) {
            $strWhere .= " AND user_name= '" . $arrCondition['user_name'] . "'";
        }
        if ($arrCondition['user_status'] !== '' && $arrCondition['user_status'] !== NULL) {
            $strWhere .= " AND user_status=" . $arrCondition['user_status'];
        }
        if ($arrCondition['not_user_status'] !== '' && $arrCondition['not_user_status'] !== NULL) {
            $strWhere .= " AND user_status !=" . $arrCondition['not_user_status'];
        }
        if (isset($arrCondition['user_email']) && $arrCondition['user_email']) {
            $strWhere .= " AND user_email='" . $arrCondition['user_email'] . "'";
        }

        if (isset($arrCondition['user_email_or_user_name']) && $arrCondition['user_email_or_user_name']) {
            $strWhere .= " AND (user_email='" . $arrCondition['user_email_or_user_name'] . "' OR user_name='" . $arrCondition['user_email_or_user_name'] . "')";
        }

        if (isset($arrCondition['name_or_email']) && $arrCondition['name_or_email']) {
            $keyword = $arrCondition['name_or_email'];
            $strWhere .= " AND ( MATCH(user_fullname, user_email) AGAINST ('" . $keyword . "'  IN BOOLEAN MODE))";
        }

        if (isset($arrCondition['random_key'])) {
            $strWhere .= " AND random_key='" . $arrCondition['random_key'] . "'";
        }

        if ($arrCondition['in_user_id'] !== '' && $arrCondition['in_user_id'] !== NULL) {
            $strWhere .= " AND user_id IN (" . $arrCondition['in_user_id'] . ")";
        }

        if ($arrCondition['user_phone'] !== '' && $arrCondition['user_phone'] !== NULL) {
            $strWhere .= " AND user_phone = '" . $arrCondition['user_phone'] . "'";
        }

        if ($arrCondition['not_grou_id'] !== '' && $arrCondition['not_grou_id'] !== NULL) {
            $strWhere .= " AND grou_id != " . $arrCondition['not_grou_id'];
        }

        if ($arrCondition['user_id_list'] !== '' && $arrCondition['user_id_list'] !== NULL) {
            $strWhere .= " AND user_id IN (" . $arrCondition['user_id_list'] . ")";
        }
        return $strWhere;
    }

}
