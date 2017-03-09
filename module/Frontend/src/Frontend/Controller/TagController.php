<?php

namespace Frontend\Controller;

use My\Controller\MyController,
    My\General;

class TagController extends MyController
{
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */
    /* @var $serviceProperties \My\Models\Properties */

    public function indexAction()
    {
        try {
            $params = $this->params()->fromRoute();

            if (empty($params['tagId']) || empty($params['tagSlug'])) {
                return $this->redirect()->toRoute('404', array());
            }
            $instanceSearchTag = new \My\Search\Tag();
            $tagDetail = $instanceSearchTag->getDetail([
                'tag_id' => $params['tagId']
            ]);

            if (empty($tagDetail)) {
                return $this->redirect()->toRoute('404', array());
            }
            if ($tagDetail['tag_slug'] != $params['tagSlug']) {
                $this->redirect()->toRoute('tag', ['tagSlug' => $tagDetail['tag_slug'], 'tagId' => $tagDetail['tag_id']]);
            }

            $intPage = (int)$params['page'] > 0 ? (int)$params['page'] : 1;
            $intLimit = 15;

            $arrCondition = [
                'cont_status' => 1,
                'search_tag_id' => '*,' . $tagDetail['tag_id'] . ',*'
            ];

            $instanceSearchContent = new \My\Search\Content();
            $arrContentList = $instanceSearchContent->getListLimit(
                $arrCondition,
                $intPage,
                $intLimit,
                ['created_date' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cont_image',
                    'tag_id',
                    'created_date'
                ]
            );

            $intTotal = $instanceSearchContent->getTotal($arrCondition);
            $helper = $this->serviceLocator->get('viewhelpermanager')->get('Paging');
            $paging = $helper($params['module'], $params['__CONTROLLER__'], $params['action'], $intTotal, $intPage, $intLimit, 'tag', $params);

            $metaDescription = $metaKeyword = $metaTitle = 'Tag ' . $tagDetail['tag_name'];

            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
            $this->renderer->headTitle(html_entity_decode($metaTitle) . General::TITLE_META);
            $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('tag', array('tagSlug' => $tagDetail['tag_slug'], 'tagId' => $tagDetail['tag_id'], 'page' => $intPage)));
            $this->renderer->headMeta()->appendName('og:url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('tag', array('tagSlug' => $tagDetail['tag_slug'], 'tagId' => $tagDetail['tag_id'], 'page' => $intPage)));
            $this->renderer->headMeta()->appendName('title', html_entity_decode('Tag : ' . $metaTitle));
            $this->renderer->headMeta()->setProperty('og:title', html_entity_decode('Tag : ' . $metaTitle));
            $this->renderer->headMeta()->appendName('keywords', html_entity_decode($metaKeyword));
            $this->renderer->headMeta()->appendName('description', html_entity_decode('Tag : ' . $metaDescription));
            $this->renderer->headMeta()->setProperty('og:description', html_entity_decode('Tag : ' . $metaDescription));
            $this->renderer->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('tag', array('tagSlug' => $tagDetail['tag_slug'], 'tagId' => $tagDetail['tag_id'], 'page' => $intPage))));
            $this->renderer->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('tag', array('tagSlug' => $tagDetail['tag_slug'], 'tagId' => $tagDetail['tag_id'], 'page' => $intPage))));

            //20 hot content in tag
            $arrContentHot = $instanceSearchContent->getListLimit(
                $arrCondition,
                1,
                20,
                ['cont_views' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cont_image'
                ]
            );

            //50 KEYWORD :)
            $instanceSearchKeyword = new \My\Search\Keyword();
            $arrKeywordList = $instanceSearchKeyword->getListLimit(
                ['full_text_keyname' => $tagDetail['tag_name']],
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
                'params' => $params,
                'paging' => $paging,
                'tagDetail' => $tagDetail,
                'arrContentList' => $arrContentList,
                'intTotal' => $intTotal,
                'arrKeywordList' => $arrKeywordList,
                'arrContentHot' => $arrContentHot
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
