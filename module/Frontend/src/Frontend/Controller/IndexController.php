<?php

namespace Frontend\Controller;

use My\Controller\MyController,
    Zend\View\Model\ViewModel;
use My\General;

class IndexController extends MyController
{
    public function indexAction()
    {
        try {
            $params = $this->params()->fromRoute();
            $params = array_merge($params, $this->params()->fromQuery());
            $intPage = (int)$params['page'] > 0 ? (int)$params['page'] : 1;
            $intLimit = 10;
            $is_ajax = empty($params['is_ajax']) ? false : true;

            $instanceSearchContent = new \My\Search\Content();
            //get 10 bài mới nhất
            $arrContentNewList = $instanceSearchContent->getListLimit(
                ['cont_status' => 1],
                $intPage,
                $intLimit,
                ['created_date' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_description',
                    'cont_id',
                    'cont_image',
                    'cont_views',
                    'cont_duration'
                ]
            );
            if ($is_ajax) {
                $viewModel = new ViewModel([
                    'arrContentList' => $arrContentNewList
                ]);
                $ajax_template = 'frontend/content-load-more';
                $viewModel->setTerminal(true)->setTemplate($ajax_template);
                $html = $this->serviceLocator->get('viewrenderer')->render($viewModel);
                return $this->getResponse()->setContent(json_encode(['status' => true, 'html' => $html]));
            }

            $arrCategoryList = unserialize(ARR_CATEGORY);

            //get bài mới nhất theo từng danh mục
            $redis = \My\General::getRedisConfig();
            $arrContentByCategory = $redis->get(\My\General::REDIS_KEY_CONT_HOME_PAGE);

            if (empty($arrContentByCategory)) {
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
                $redis->set(\My\General::REDIS_KEY_CONT_HOME_PAGE, json_encode($arrContentByCategory));
            } else {
                $arrContentByCategory = json_decode($arrContentByCategory, true);
            }

            //get hot top day
            $arrHotTopDay = $redis->get(\My\General::REDIS_KEY_CONTENT_TOP_DAY);

            if (empty($arrHotTopDay)) {
                $instanceSearchContentView = new \My\Search\ContentView();
                $condition = [
                    'created_date_lte' => date('Y-m-d'),
                    'created_date_gte' => date('Y-m-d', strtotime('-1 days')),
                ];
                $arrHotTopDay = $instanceSearchContentView->getContentTopDay($condition);
                if (!empty($arrContent)) {
                    $redis->set(\My\General::REDIS_KEY_CONTENT_TOP_DAY, json_encode($arrHotTopDay));
                }
            } else {
                $arrHotTopDay = json_decode($arrHotTopDay, true);
            }

            //get hot top week
            $arrHotTopWeek = $redis->get(\My\General::REDIS_KEY_CONTENT_TOP_WEEK);
            if (empty($arrHotTopWeek)) {
                $instanceSearchContentView = new \My\Search\ContentView();
                $condition = [
                    'created_date_lte' => date('Y-m-d'),
                    'created_date_gte' => date('Y-m-d', strtotime('-7 days')),
                ];
                $limit = 20;
                $arrContent = $instanceSearchContentView->getContentTopDay($condition, $limit);
                if (!empty($arrContent)) {
                    $redis->set(\My\General::REDIS_KEY_CONTENT_TOP_WEEK, json_encode($arrContent));
                }
            } else {
                $arrHotTopWeek = json_decode($arrHotTopWeek, true);
            }

            $redis->close();

            return [
                'arrContentNewList' => $arrContentNewList,
                'arrContentByCategory' => $arrContentByCategory,
                'arrCategoryList' => $arrCategoryList,
                'arrHotContentList' => $arrHotTopDay,
                'arrHotTopWeek' => $arrHotTopWeek
            ];
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
            return $this->redirect()->toRoute('404', array());
        }
    }

    public function getContentHomePage()
    {

    }

}
