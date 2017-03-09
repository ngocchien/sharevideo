<?php

namespace Frontend\Controller;

use My\Controller\MyController,
    My\General;

class SearchController extends MyController
{
    public function indexAction()
    {
        try {
            $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());

            if (empty($params['keyword'])) {
                return $this->redirect()->toRoute('404');
            }

            $key_name = General::clean($params['keyword']);
            $intPage = (int)$params['page'] > 0 ? (int)$params['page'] : 1;
            $intLimit = 20;

            $arr_condition_content = [
                'cont_status' => 1,
                'full_text_title' => $key_name
            ];

            $instanceSearchContent = new \My\Search\Content();
            $arrContentList = $instanceSearchContent->getListLimit(
                $arr_condition_content,
                $intPage,
                $intLimit,
                [
                    '_score' => ['order' => 'desc'],
                    'cont_id' => ['order' => 'desc']
                ],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cate_id',
                    'cont_image',
                    'created_date'
                ]
            );

            //phân trang
            $intTotal = $instanceSearchContent->getTotal($arr_condition_content);
            $helper = $this->serviceLocator->get('viewhelpermanager')->get('Paging');
            $paging = $helper($params['module'], $params['__CONTROLLER__'], $params['action'], $intTotal, $intPage, $intLimit, 'search', $params);

            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
            $this->renderer->headTitle(html_entity_decode('Search - ' . $params['keyword'] . General::TITLE_META));
            $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('search', ['keyword' => $params['keyword'], 'page' => $intPage]));
            $this->renderer->headMeta()->appendName('og:url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('search', ['keyword' => $params['keyword'], 'page' => $intPage]));
            $this->renderer->headMeta()->appendName('title', html_entity_decode('Search - ' . $params['keyword'] . General::TITLE_META));
            $this->renderer->headMeta()->setProperty('og:title', html_entity_decode('Search - ' . $params['keyword'] . General::TITLE_META));
            $this->renderer->headMeta()->appendName('keywords', html_entity_decode($params['keyword'], str_replace('')));
            $this->renderer->headMeta()->appendName('description', html_entity_decode($params['keyword']));
            $this->renderer->headMeta()->setProperty('og:description', html_entity_decode($params['keyword']));

            $this->renderer->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('search', ['keyword' => $params['keyword']])));
            $this->renderer->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('search', ['keyword' => $params['keyword']])));

            //get 40 keyword gần giống nhất
            $instanceSearchKeyword = new \My\Search\Keyword();
            $arrKeywordList = $instanceSearchKeyword->getListLimit(
                ['full_text_keyname' => $key_name],
                $intPage,
                40,
                ['_score' => ['order' => 'desc']],
                [
                    'key_id',
                    'key_name',
                    'key_slug'
                ]
            );

            return [
                'paging' => $paging,
                'params' => $params,
                'arrContentList' => $arrContentList,
                'arrKeywordList' => $arrKeywordList,
                'intTotal' => $intTotal
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

    public function keywordAction()
    {
        try {
            $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());
            $key_id = (int)$params['keyId'];
            $key_slug = $params['keySlug'];
            $intPage = is_numeric($params['page']) ? $params['page'] : 1;
            $intLimit = 20;

            if (empty($key_id)) {
                return $this->redirect()->toRoute('404', array());
            }

            $instanceSearch = new \My\Search\Keyword();
            $arrKeyDetail = $instanceSearch->getDetail(['key_id' => $key_id]);

            if (empty($arrKeyDetail)) {
                return $this->redirect()->toRoute('404', array());
            }

            if ($arrKeyDetail['key_slug'] != $key_slug) {
                return $this->redirect()->toRoute('keyword', ['keySlug' => $arrKeyDetail['key_slug'], 'keyId' => $arrKeyDetail['key_id'], 'page' => $intPage]);
            }

            $instanceSearchContent = new \My\Search\Content();
            $arrContentList = $instanceSearchContent->getListLimit(
                ['full_text_title' => $arrKeyDetail['key_name']],
                $intPage,
                $intLimit,
                [
                    '_score' => ['order' => 'desc'],
                    'cont_id' => ['order' => 'desc']
                ],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_description',
                    'cont_id',
                    'cont_image',
                    'created_date',
                    'cont_duration'
                ]
            );

            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');

            $this->renderer->headTitle(html_entity_decode($arrKeyDetail['key_name']));
            $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('keyword', array('keySlug' => $arrKeyDetail['key_slug'], 'keyId' => $arrKeyDetail['key_id'], 'page' => $intPage)));
            $this->renderer->headMeta()->appendName('og:url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('keyword', array('keySlug' => $arrKeyDetail['key_slug'], 'keyId' => $arrKeyDetail['key_id'], 'page' => $intPage)));
            $this->renderer->headMeta()->appendName('title', html_entity_decode($arrKeyDetail['key_name']));
            $this->renderer->headMeta()->setProperty('og:title', html_entity_decode($arrKeyDetail['key_name']));
            $this->renderer->headMeta()->appendName('keywords', html_entity_decode($arrKeyDetail['key_name']));
            $this->renderer->headMeta()->appendName('description', html_entity_decode($arrKeyDetail['key_description']) ?: 'List post in keyword : ' . $arrKeyDetail['key_name']);
            $this->renderer->headMeta()->setProperty('og:description', html_entity_decode($arrKeyDetail['key_description']) ?: 'List post in keyword : ' . $arrKeyDetail['key_name']);

            $this->renderer->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('keyword', array('keySlug' => $arrKeyDetail['key_slug'], 'keyId' => $arrKeyDetail['key_id']))));
            $this->renderer->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('keyword', array('keySlug' => $arrKeyDetail['key_slug'], 'keyId' => $arrKeyDetail['key_id']))));

            /*
             * get 40 keyword tương tự
             */
            $arrKeywordList = $instanceSearch->getListLimit(
                [
                    'full_text_keyname' => $arrKeyDetail['key_name'],
                    'not_key_id' => $key_id],
                $intPage,
                40,
                ['_score' => ['order' => 'desc']],
                [
                    'key_id',
                    'key_name',
                    'key_slug'
                ]
            );

            return array(
                'arrKeywordList' => $arrKeywordList,
                'arrContentList' => $arrContentList,
                'arrKeyDetail' => $arrKeyDetail
            );
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

    public function listKeywordAction()
    {
        try {
            $params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());
            $intPage = is_numeric($params['page']) ? $params['page'] : 1;
            $intLimit = 100;

            $instanceSearch = new \My\Search\Keyword();

            $arrCondition = array(
                'word_id_less' => round((time() - 1465036100) / 4)
            );
            $arrKeywordList = $instanceSearch->getListLimit(
                $arrCondition,
                $intPage,
                $intLimit,
                ['key_id' => ['order' => 'desc']],
                [
                    'key_id',
                    'key_name',
                    'key_slug'
                ]
            );
            $intTotal = $instanceSearch->getTotal($arrCondition);
            $helper = $this->serviceLocator->get('viewhelpermanager')->get('Paging');
            $paging = $helper($params['module'], $params['__CONTROLLER__'], $params['action'], $intTotal, $intPage, $intLimit, 'list-keyword', $params);

            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
            $this->renderer->headTitle(html_entity_decode('Danh sách từ khoá trang ' . $intPage) . \My\General::TITLE_META);
            $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('list-keyword', array('page' => $intPage)));
            $this->renderer->headMeta()->appendName('og:url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('list-keyword', array('page' => $intPage)));
            $this->renderer->headMeta()->appendName('title', html_entity_decode('Danh sách từ khoá trang ' . $intPage) . General::TITLE_META);
            $this->renderer->headMeta()->setProperty('og:title', html_entity_decode('Danh sách từ khoá trang ' . $intPage) . General::TITLE_META);
            $this->renderer->headMeta()->appendName('keywords', html_entity_decode('Danh sách từ khoá trang ' . $intPage));
            $this->renderer->headMeta()->appendName('description', html_entity_decode('Danh sách từ khoá trang ' . $intPage . General::TITLE_META));
            $this->renderer->headMeta()->appendName('og.description', html_entity_decode('Danh sách từ khoá trang ' . $intPage) . General::TITLE_META);
            $this->renderer->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('list-keyword', array())));
            $this->renderer->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('list-keyword', array())));

            return array(
                'params' => $params,
                'arrKeywordList' => $arrKeywordList,
                'paging' => $paging,
                'intPage' => $intPage,
                'intLimit' => $intLimit,
                'intTotal' => $intTotal,
                'title' => 'Keyword'
            );
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
