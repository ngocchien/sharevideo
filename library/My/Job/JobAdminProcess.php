<?php

namespace My\Job;

use My;

class JobAdminProcess extends JobAbstract
{
    /* update */

    public function updateDataDB($params, $serviceLocator)
    {
        try {
            if ($params->workload()) {
                $arrParams = unserialize($params->workload());
            }

            if (empty($arrParams)) {
                echo General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
                return false;
            }

            $object_name = $arrParams['object_name'];
            $object_id = $arrParams['object_id'];
            $arrData = $arrParams['data'];
            $service = self::__buildService($object_name, $serviceLocator);
            $result = $service->edit($arrData, $object_id);

            if (!$result) {
                echo General::getColoredString("ERROR: Cannot Edit id = {$object_id} to Search \n", 'light_cyan', 'red');
                return false;
            }

            //update table content view
            if ($object_name == 'content' && !empty($arrParams['is_update_view'])) {
                $current_date = date('Y-m-d');
//                $current_date = '2017-03-03';
                $instance = self::__buildInstance('cont-view');
                $service = self::__buildService('cont-view', $serviceLocator);
                $contentView = $instance->getDetail([
                    'created_date' => $current_date,
                    'cont_id' => $object_id
                ]);

                $result = false;
                if (empty($contentView)) {
                    //insert vào table
                    $result = $service->add([
                        'cont_id' => $object_id,
                        'created_date' => $current_date,
                        'updated_date' => time(),
                        'view' => 1,
                    ]);
                } else {
                    //update vào table
                    $result = $service->edit(
                        [
                            'updated_date' => time(),
                            'view' => $contentView['view'] + 1,
                        ],
                        $contentView['id']
                    );
                }

                if ($result) {
                    echo General::getColoredString("update view content success content_id: {$object_id}", 'green');
                }
            }

            echo General::getColoredString("Edit id: {$object_id} to Search Success", 'green');
            return true;

        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r([
                    'code' => $exc->getCode(),
                    'messages' => $exc->getMessage()
                ]);
                echo '</pre>';
                die();
            }
            return true;
        }
    }

    public static function __buildInstance($object_name)
    {
        $instance = '';
        switch ($object_name) {
            case 'category':
                $instance = new \My\Search\Category();
                break;
            case 'content':
                $instance = new \My\Search\Content();
                break;
            case 'keyword':
                $instance = new \My\Search\Keyword();
                break;
            case 'tag':
                $instance = new \My\Search\Tag();
                break;
            case 'user':
                $instance = new \My\Search\User();
                break;
            case 'cont-view':
                $instance = new \My\Search\ContentView();
                break;
        }
        return $instance;
    }

    public static function __buildService($object_name, $serviceLocator)
    {
        $service = '';
        switch ($object_name) {
            case 'category':
                $service = $serviceLocator->get('My\Models\Category');
                break;
            case 'content':
                $service = $serviceLocator->get('My\Models\Content');
                break;
            case 'keyword':
                $service = $serviceLocator->get('My\Models\Keyword');
                break;
            case 'tag':
                $service = $serviceLocator->get('My\Models\Tags');
                break;
            case 'user':
                $service = $serviceLocator->get('My\Models\User');
                break;
            case
            'cont-view':
                $service = $serviceLocator->get('My\Models\ContentView');
                break;
        }
        return $service;
    }

    public function buildDataRedisContent($params = [])
    {
        if ($params->workload()) {
            $arrParams = unserialize($params->workload());
        }

        if (empty($arrParams)) {
            echo My\General::getColoredString("ERROR: Params is incorrent or empty ", 'light_cyan', 'red');
        }

        $instanceSearchContent = new My\Search\Content();
        try {
            switch ($arrParams['type']) {
                case 'content-home-page':
                    $instanceCategory = new My\Search\Category();
                    $arrCategoryList = $instanceCategory->getList(
                        [
                            'cate_status' => 1
                        ],
                        [
                            'cate_id'
                        ]
                    );

                    if (!empty($arrCategoryList)) {
                        $arrContentByCategory = [];
                        foreach ($arrCategoryList as $arrCategory) {
                            $arrContentByCategory[$arrCategory['cate_id']] = $instanceSearchContent->getListLimit(
                                [
                                    'cont_status' => 1,
                                    'cate_id' => $arrCategory['cate_id']
                                ],
                                1,
                                6,
                                ['created_date' => ['order' => 'desc']],
                                [
                                    'cont_title',
                                    'cont_slug',
                                    'cont_id',
                                    'cont_image',
                                    'cont_views',
                                    'cont_duration'
                                ]
                            );
                        }
                        if (!empty($arrContentByCategory)) {
                            $redis = My\General::getRedisConfig();
                            $redis->set(My\General::REDIS_KEY_CONT_HOME_PAGE, json_encode($arrContentByCategory));
                            $redis->close();
                        }
                    }
                    break;
                case 'top-content-hot-now':
                    $instanceSearchContentView = new My\Search\ContentView();
                    $condition = [
                        'created_date_lte' => date('Y-m-d'),
                        'created_date_gte' => date('Y-m-d', strtotime('-1 days')),
                    ];
                    $limit = 20;
                    $arrContent = $instanceSearchContentView->getContentTopDay($condition, $limit);
                    if (!empty($arrContent)) {
                        $redis = My\General::getRedisConfig();
                        $redis->set(My\General::REDIS_KEY_CONTENT_TOP_DAY, json_encode($arrContent));
                        $redis->close();
                    }
                    break;
                case 'top-content-hot-week':
                    $instanceSearchContentView = new My\Search\ContentView();
                    $condition = [
                        'created_date_lte' => date('Y-m-d'),
                        'created_date_gte' => date('Y-m-d', strtotime('-7 days')),
                    ];
                    $limit = 20;
                    $arrContent = $instanceSearchContentView->getContentTopDay($condition, $limit);
                    if (!empty($arrContent)) {
                        $redis = My\General::getRedisConfig();
                        $redis->set(My\General::REDIS_KEY_CONTENT_TOP_WEEK, json_encode($arrContent));
                        $redis->close();
                    }
                    break;
                default:
                    break;
            }
        } catch (\Exception $exc) {
            if (APPLICATION_ENV !== 'production') {
                echo '<pre>';
                print_r($exc->getMesseges());
                echo '</pre>';
                die();
            }
        }
    }

}
