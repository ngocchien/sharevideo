<?php

namespace My\Search;

use Elastica\Query\QueryString,
    Elastica\Type\Mapping,
    Elastica\Query\Bool,
    Elastica\Search,
    Elastica\Status,
    Elastica\Query as ESQuery,
    My\General;

class Permission extends SearchAbstract {

    public function __construct() {
        $this->setSearchIndex(SEARCH_PREFIX . 'permission');
        $this->setSearchType('permissionList');
    }

    public function createIndex() {
        $strIndexName = SEARCH_PREFIX . 'permission';

        $searchClient = General::getSearchConfig();
        $searchIndex = $searchClient->getIndex($strIndexName);

        $objStatus = new Status($searchIndex->getClient());
        $arrIndex = $objStatus->getIndexNames();

        //delete index
        if (in_array($strIndexName, $arrIndex)) {
            $searchIndex->delete();
        }

        //create new index
        $searchIndex->create([
            'name' => 'translations',
            'number_of_shards' => 2,
            'number_of_replicas' => 0,
            'analysis' => [
                'analyzer' => [
                    'translation_index_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['standard', 'lowercase', 'asciifolding', 'trim']
                    ],
                    'translation_search_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['standard', 'lowercase', 'asciifolding', 'trim']
                    ]
                ]
            ],
            'filter' => [
                'translation' => [
                    'type' => 'edgeNGram',
                    'token_chars' => ["letter", "digit", " whitespace"],
                    'min_gram' => 1,
                    'max_gram' => 30,
                ]
            ],
                ], true);

        //set search type
        $searchType = $searchIndex->getType('permissionList');
        $mapping = new Mapping();
        $mapping->setType($searchType);
        $mapping->setProperties([
            'perm_id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'module' => ['type' => 'string', 'index' => 'not_analyzed'],
            'controller' => ['type' => 'string', 'index' => 'not_analyzed'],
            'action' => ['type' => 'string', 'index' => 'not_analyzed'],
            'group_id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'user_created' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'created_date' => ['type' => 'long', 'index' => 'not_analyzed'],
            'updated_date' => ['type' => 'long', 'index' => 'not_analyzed'],
            'user_updated' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'user_id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'perm_status' => ['type' => 'integer', 'index' => 'not_analyzed']
        ]);
        $mapping->send();
    }

    public function add($arrDocument) {
        try {
            if (empty($arrDocument) && !$arrDocument instanceof \Elastica\Document) {
                throw new \Exception('Document cannot be blank or must be instance of \Elastica\Document class');
            }
            $arrDocument = is_array($arrDocument) ? $arrDocument : [$arrDocument];
            $this->getSearchType()->addDocuments($arrDocument);
            $this->getSearchType()->getIndex()->refresh();
            return true;
        } catch (\Zend\Http\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                throw new \Zend\Http\Exception($exc->getMessage());
            }
            return false;
        }
    }

    public function edit($document) {
        $respond = $this->getSearchType()->updateDocument($document);
        $this->getSearchType()->getIndex()->refresh();
        if ($respond->isOk()) {
            return true;
        }
        return false;
    }

    public function getDetail($params, $arrFields = []) {
        try {
            $boolQuery = new Bool();
            $boolQuery = $this->__buildWhere($params, $boolQuery);
            $query = new ESQuery();
            $query->setQuery($boolQuery);

            if ($arrFields && is_array($arrFields)) {
                $query->setSource($arrFields);
            }

            $instanceSearch = new Search(General::getSearchConfig());
            $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
                    ->addType($this->getSearchType())
                    ->search($query);
            $this->setResultSet($resultSet);
            return current($this->toArray());
        } catch (\Exception $exc) {
            echo '<pre>';
            print_r($exc->getMessage());
            echo '</pre>';
            die();
        }
    }

    /**
     * Get List Limit
     */
    public function getListLimit($params = array(), $intPage = 1, $intLimit = 15, $sort = ['updated_date' => ['order' => 'desc']]) {
        try {
            $intFrom = $intLimit * ($intPage - 1);
            $boolQuery = new Bool();
            $boolQuery = $this->__buildWhere($params, $boolQuery);
            $query = new ESQuery();
            $query->setFrom($intFrom)
                    ->setSize($intLimit)
                    ->setSort($sort);
            $query->setQuery($boolQuery);
            $instanceSearch = new Search(General::getSearchConfig());
            $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
                    ->addType($this->getSearchType())
                    ->search($query);
            $this->setResultSet($resultSet);
            return $this->toArray();
        } catch (\Exception $exc) {
            echo $exc->getMessage();
            die;
        }
    }

    /**
     * Get List
     */
    public function getList($params, $arrFields = [], $sort = ['perm_id' => ['order' => 'asc']]) {
        $boolQuery = new Bool();
        $boolQuery = $this->__buildWhere($params, $boolQuery);
        $query = new ESQuery();

        $total = $this->getTotal($params);
        if (empty($sort)) {
            $sort = $this->setSort($params);
        }
        $query->setSize($total)
                ->setSort($sort);
        $query->setQuery($boolQuery);
        if ($arrFields && is_array($arrFields)) {
            $query->setSource($arrFields);
        }
        
        $instanceSearch = new Search(General::getSearchConfig());
        $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
                ->addType($this->getSearchType())
                ->search($query);
        $this->setResultSet($resultSet);
        return $this->toArray();
    }

    /**
     * get Total
     * @param array $arrConditions
     * @return integer
     */
    public function getTotal($arrConditions = array()) {
        $boolQuery = new Bool();
        $boolQuery = $this->__buildWhere($arrConditions, $boolQuery);
        $query = new ESQuery();
        $query->setQuery($boolQuery);
        $instanceSearch = new Search(General::getSearchConfig());
        $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
                ->addType($this->getSearchType())
                ->count($query);
        return $resultSet;
    }

    private function setSort($params) {
        //copy
        return ['updated_date' => ['order' => 'desc']];
    }

    public function removeAllDoc() {
        $respond = $this->getSearchType()->deleteByQuery('_type:permissionList');
        $this->getSearchType()->getIndex()->refresh();
        if ($respond->isOk()) {
            return true;
        }
        return false;
    }

    public function __buildWhere($params, $boolQuery) {

        if (empty($params)) {
            return $boolQuery;
        }

        if (!empty($params['perm_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('perm_id', $params['perm_id']);
            $boolQuery->addMust($addQuery);
        }
        
        if (!empty($params['in_perm_id'])) {
            $addQuery = new ESQuery\Terms();
            $addQuery->setTerms('perm_id', $params['in_perm_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['perm_status'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('perm_status', $params['perm_status']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['not_group_status'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('group_status', $params['not_group_status']);
            $boolQuery->addMustNot($addQuery);
        }

        if (!empty($params['not_perm_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('perm_id', $params['not_perm_id']);
            $boolQuery->addMustNot($addQuery);
        }

        if (!empty($params['group_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('group_id', $params['group_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['user_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('user_id', $params['user_id']);
            $boolQuery->addMust($addQuery);
        }

        return $boolQuery;
    }

}
