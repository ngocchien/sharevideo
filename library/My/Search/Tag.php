<?php

namespace My\Search;

use Elastica\Query\QueryString,
    Elastica\Type\Mapping,
    Elastica\Query\Bool,
    Elastica\Search,
    Elastica\Status,
    Elastica\Query as ESQuery,
    My\General;

class Tag extends SearchAbstract
{

    public function __construct()
    {
        $this->setSearchIndex(SEARCH_PREFIX . 'tag');
        $this->setSearchType('tagList');
    }

    public function createIndex()
    {
        $strIndexName = SEARCH_PREFIX . 'tag';

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
        $searchType = $searchIndex->getType('tagList');
        $mapping = new Mapping();
        $mapping->setType($searchType);
        $mapping->setProperties([
            'tag_id' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'tag_name' => ['type' => 'string', 'store' => 'yes', 'analyzer' => 'translation_index_analyzer', 'search_analyzer' => 'translation_search_analyzer', 'term_vector' => 'with_positions_offsets'],
            'tag_slug' => ['type' => 'string', 'index' => 'not_analyzed'],
            'created_date' => ['type' => 'long', 'index' => 'not_analyzed'],
            'user_created' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'updated_date' => ['type' => 'long', 'index' => 'not_analyzed'],
            'user_updated' => ['type' => 'integer', 'index' => 'not_analyzed'],
            'tag_status' => ['type' => 'integer', 'index' => 'not_analyzed']
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
        $detailAreaPrice = current($this->toArray());
        return $detailAreaPrice;
    }

    /**
     * Get List Limit
     */
    public function getListLimit($params = array(), $intPage = 1, $intLimit = 15, $sort = ['created_date' => ['order' => 'desc']])
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
    public function getList($params, $sort = [], $arrFields = [])
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
        return ['tag_id' => ['order' => 'desc']];
    }

    public function removeAllDoc()
    {
        $respond = $this->getSearchType()->deleteByQuery('_type:tagList');
        $this->getSearchType()->getIndex()->refresh();
        if ($respond->isOk()) {
            return true;
        }
        return false;
    }

    public function __buildWhere($params, $boolQuery)
    {

        if (empty($params)) {
            return $boolQuery;
        }

        if (!empty($params['tag_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('tag_id', $params['tag_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['in_tag_id'])) {
            $addQuery = new ESQuery\Terms();
            $addQuery->setTerms('tag_id', $params['in_tag_id']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['not_tag_id'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('tag_id', $params['not_tag_id']);
            $boolQuery->addMustNot($addQuery);
        }

        if (!empty($params['user_created'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('user_created', $params['user_created']);
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['tag_status'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('tag_status', $params['tag_status']);
            $boolQuery->addMust($addQuery);
        }

        if (isset($params['gte_tag_id'])) {
            $addQuery = new ESQuery\Range();
            $addQuery->addField('tag_id', array('gte' => $params['gte_tag_id']));
            $boolQuery->addMust($addQuery);
        }

        if (isset($params['lte_tag_id'])) {
            $addQuery = new ESQuery\Range();
            $addQuery->addField('tag_id', array('lte' => $params['lte_tag_id']));
            $boolQuery->addMust($addQuery);
        }

        if (!empty($params['not_tag_status'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('tag_status', $params['not_tag_status']);
            $boolQuery->addMustNot($addQuery);
        }

        if (!empty($params['tag_slug'])) {
            $addQuery = new ESQuery\Term();
            $addQuery->setTerm('tag_slug', $params['tag_slug']);
            $boolQuery->addMust($addQuery);
        }

        if (isset($params['full_text_title'])) {
            $math = new ESQuery\Match();
            $math->setParam('tag_name', trim($params['full_text_title']));
            $boolQuery->addMust($math);
        }

        if (!empty($params['in_tag_slug'])) {
            $addQuery = new ESQuery\Terms();
            $addQuery->setTerms('tag_slug', $params['in_tag_slug']);
            $boolQuery->addMust($addQuery);
        }

        if (isset($params['full'])) {
            $wordNameQueryString = new ESQuery\QueryString();
            $wordNameQueryString->setDefaultField('tag_name')
                ->setQuery('*');
            $boolQuery->addMust($wordNameQueryString);
        }

        return $boolQuery;
    }

}
