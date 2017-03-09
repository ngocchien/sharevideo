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

            //get bài mới nhất theo từng danh mục
            $arrCategoryList = unserialize(ARR_CATEGORY);
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

            //get top 40 post view
            $arrHotContentList = $instanceSearchContent->getListLimit(
                [
                    'cont_status' => 1
                ],
                1,
                30,
                ['cont_views' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_id',
                    'cont_image',
                    'cont_views'
                ]
            );

            return [
                'arrContentNewList' => $arrContentNewList,
                'arrContentByCategory' => $arrContentByCategory,
                'arrCategoryList' => $arrCategoryList,
                'arrHotContentList' => $arrHotContentList
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

}
