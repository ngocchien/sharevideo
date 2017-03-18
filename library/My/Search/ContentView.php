<?php

namespace My\Search;

use Elastica\Aggregation\Sum;
use Elastica\Aggregation\Terms;
use Elastica\Filter\BoolAnd;
use Elastica\Query;
use Elastica\Query\QueryString,
    Elastica\Type\Mapping,
    Elastica\Query\Bool,
    Elastica\Search,
    Elastica\Status,
    Elastica\Query as ESQuery,
    My\General;

class ContentView extends SearchAbstract
{

    public function __construct()
    {
        $this->setSearchIndex(SEARCH_PREFIX . 'contentview');
        $this->setSearchType('contentviewList');
    }

    public function createIndex()
    {
        $strIndexName = SEARCH_PREFIX . 'contentview';

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
            'number_of_shards' => 5,
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
        $searchType = $searchIndex->getType('categoryList');
        $mapping = new Mapping();
        $mapping->setType($searchType);
        $mapping->setProperties([
            'id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'cont_id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'created_date' => ['type' => 'date', 'index' => 'not_analyzed', 'format' => 'yyyy-MM-dd'],
            'updated_date' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'view' => ['type' => 'integer', 'index' => 'not_analyzed']
        ]);
        $mapping->send();
    }

    public function add($arrDocument)
    {
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

    public function edit($document)
    {
        $respond = $this->getSearchType()->updateDocument($document);
        $this->getSearchType()->getIndex()->refresh();
        if ($respond->isOk()) {
            return true;
        }
        return false;
    }

    public function getDetail($params, $arrFields = [])
    {
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
    public function getListLimit($params = array(), $intPage = 1, $intLimit = 15, $sort = ['updated_date' => ['order' => 'desc']], $arrFields = [])
    {
        try {
            $intFrom = $intLimit * ($intPage - 1);
            $boolQuery = new Bool();
            $boolQuery = $this->__buildWhere($params, $boolQuery);
            $query = new ESQuery();
            $query->setFrom($intFrom)
                ->setSize($intLimit)
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
        } catch (\Exception $exc) {
            echo $exc->getMessage();
            die;
        }
    }

    /**
     * Get List
     */
    public function getList($params, $arrFields = [], $sort = ['id' => ['order' => 'asc']])
    {
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
    public function getTotal($arrConditions = array())
    {
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

    private function setSort($params)
    {
        //copy
        return ['updated_date' => ['order' => 'desc']];
    }

    public function removeAllDoc()
    {
        $respond = $this->getSearchType()->deleteByQuery('_type:categoryList');
        $this->getSearchType()->getIndex()->refresh();
        if ($respond->isOk()) {
            return true;
        }
        return false;
    }

    public function getContentTopWeek($arrConditions = [], $limit = 15)
    {
        $termsAgg = new \Elastica\Aggregation\Terms("group_by");
        $termsAgg->setField("cont_id")->setOrder('sum', 'DESC')->setSize($limit);

        $agg = new Sum('sum');
        $agg->setField('view');
        $termsAgg->addAggregation($agg);

        $boolQuery = new Bool();
        $boolQuery = $this->__buildWhere($arrConditions, $boolQuery);

        $query = new ESQuery();
        $query->setQuery($boolQuery)->addAggregation($termsAgg);
        $instanceSearch = new Search(General::getSearchConfig());
        $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
            ->addType($this->getSearchType())
            ->search($query)
            ->getAggregation('group_by');
        $key_id = [];
        $arr_data_temp = [];
        foreach ($resultSet['buckets'] as $result) {
            $key_id[] = $result['key'];
            $arr_data_temp[$result['key']] = '';
        }

        $instanceSearchContent = new \My\Search\Content();
        $arr_content = $instanceSearchContent->getList(
            [
                'in_cont_id' => $key_id,
            ],
            [],
            [
                'cont_id',
                'cont_views',
                'cont_title',
                'cont_slug',
                'created_date',
                'user_created',
                'cont_description',
                'cont_image'
            ]
        );

        foreach ($arr_content as $value) {
            $arr_data_temp[$value['cont_id']] = $value;
        }
        return array_values($arr_data_temp);
    }

    public function getContentTopDay($arrConditions = [], $limit = 15)
    {
        $boolQuery = new Bool();
        $boolQuery = $this->__buildWhere($arrConditions, $boolQuery);

        $query = new ESQuery();
        $query->setSize($limit)
            ->setSort(['view' => ['order' => 'desc']]);
        $query->setQuery($boolQuery);
        $query->setSource(['cont_id']);

        $instanceSearch = new Search(General::getSearchConfig());
        $resultSet = $instanceSearch->addIndex($this->getSearchIndex())
            ->addType($this->getSearchType())
            ->search($query);

        $arr_cont_id = [];
        $arr_data_temp = [];
        foreach ($resultSet as $result) {
            $arr_cont_id[] = $result->getSource()['cont_id'];
            $arr_data_temp[$result->getSource()['cont_id']] = '';
        }

        $instanceSearchContent = new \My\Search\Content();
        $arr_content = $instanceSearchContent->getList(
            [
                'in_cont_id' => $arr_cont_id,
            ],
            [],
            [
                'cont_id',
                'cont_views',
                'cont_title',
                'cont_slug',
                'created_date',
                'user_created',
                'cont_description',
                'cont_image'
            ]
        );

        foreach ($arr_content as $value) {
            $arr_data_temp[$value['cont_id']] = $value;
        }
        return array_values($arr_data_temp);
    }

    public function __buildWhere($params, $boolQuery)
    {

        if (empty($params)) {
            return $boolQuery;
        }

        if (!empty($params['cont_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('cont_id', $params['cont_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['in_cont_id'])) {
            $addQuery = new ESQuery\Terms();
            $addQuery->setTerms('cont_id', $params['in_cont_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['created_date'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('created_date', $params['created_date']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('id', $params['id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['created_date_gte'])) {
            $addQuery = new ESQuery\Range();
            $addQuery->addField('created_date', array('gte' => $params['created_date_gte']));
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['created_date_lte'])) {
            $addQuery = new ESQuery\Range();
            $addQuery->addField('created_date', array('lte' => $params['created_date_lte']));
            $boolQuery->addMust($addQuery);
        }

        return $boolQuery;
    }

}
