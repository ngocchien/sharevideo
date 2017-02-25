<?php

namespace Frontend\Controller;

use My\Controller\MyController,
    My\General;

class ContentController extends MyController
{
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */
    /* @var $serviceProperties \My\Models\Properties */
    /* @var $serviceDistrict \My\Models\District */
    /* @var $serviceComment \My\Models\Comment */

    public function __construct()
    {

    }

    public function detailAction()
    {
        try {
            $params = $this->params()->fromRoute();
            $cont_id = (int)$params['contentId'];
            $cont_slug = $params['contentSlug'];

            if (empty($cont_id) || empty($cont_slug)) {
                return $this->redirect()->toRoute('404', []);
            }
            $arrConditionContent = [
                'cont_id' => $cont_id,
                'not_cont_status' => -1
            ];
            $instanceSearchContent = new \My\Search\Content();
            $arrContent = $instanceSearchContent->getDetail($arrConditionContent);

            if (empty($arrContent)) {
                return $this->redirect()->toRoute('404', []);
            }

            if ($cont_slug != $arrContent['cont_slug']) {
                return $this->redirect()->toRoute('view-content', ['contentSlug' => $arrContent['cont_slug'], 'contentId' => $cont_id]);
            }

            //update số lần view
            $serviceContent = $this->serviceLocator->get('My\Models\Content');
            $serviceContent->edit(['cont_views' => $arrContent['cont_views'] + 1, 'modified_date' => time()], $cont_id);

            /*
             render meta
            */
            $metaTitle = $arrContent['meta_title'] ? $arrContent['meta_title'] : $arrContent['cont_title'];
            $metaKeyword = $arrContent['meta_keyword'] ? $arrContent['meta_keyword'] . ',' . $arrContent['cont_title'] : $arrContent['cont_title'];
            $metaDescription = $arrContent['cont_description'] ? $arrContent['cont_description'] : $arrContent['cont_title'];

            /*
             * Category
             */
            $arrCategoryDetail = unserialize(ARR_CATEGORY)[$arrContent['cate_id']];

            //image
            $cont_detail_image = json_decode($arrContent['cont_image'], true);

            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
            $this->renderer->headTitle(html_entity_decode($metaTitle) . General::TITLE_META);
            $this->renderer->headMeta()->setProperty('url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('view-content', ['contentSlug' => $arrContent['cont_slug'], 'contentId' => $cont_id]));
            $this->renderer->headMeta()->appendName('og:url', \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('view-content', ['contentSlug' => $arrContent['cont_slug'], 'contentId' => $cont_id]));
            $this->renderer->headMeta()->appendName('title', html_entity_decode($metaTitle) . General::TITLE_META);
            $this->renderer->headMeta()->setProperty('og:title', html_entity_decode($metaTitle) . General::TITLE_META);
            $this->renderer->headMeta()->appendName('keywords', html_entity_decode($metaKeyword));
            $this->renderer->headMeta()->appendName('description', html_entity_decode($metaDescription));
            $this->renderer->headMeta()->setProperty('og:description', html_entity_decode($metaDescription));
            $this->renderer->headMeta()->appendName('image', $cont_detail_image['640x480']);
            $this->renderer->headMeta()->setProperty('og:image', $cont_detail_image['640x480']);
            $this->renderer->headMeta()->setProperty('og:image:width', '640');
            $this->renderer->headMeta()->setProperty('og:image:height', '480');

            $this->renderer->headLink(array('rel' => 'image_src', 'href' => $arrContent['cont_main_image']));
            $this->renderer->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('view-content', ['contentSlug' => $arrContent['cont_slug'], 'contentId' => $cont_id])));
            $this->renderer->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL . $this->url()->fromRoute('view-content', ['contentSlug' => $arrContent['cont_slug'], 'contentId' => $cont_id])));

            //lấy tin cũ hơn cùng chuyên mục
            $arrContentLastedList = $instanceSearchContent->getListLimit(
                [
                    'cate_id' => $arrContent['cate_id'],
                    'not_cont_status' => -1,
                    'less_cont_id' => $arrContent['cont_id']
                ],
                1,
                10,
                ['cont_id' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cate_id',
                    'cont_image',
                    'from_source'
                ]
            );

            //Lấy tin có nội dung title gần giống nhau
            $arrContentLikeList = $instanceSearchContent->getListLimit(
                [
                    'cont_status' => 1,
                    'full_text_title' => $arrContent['cont_title'],
                    'not_cont_id' => $arrContent['cont_id']
                ],
                1,
                10,
                ['_score' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cate_id',
                    'cont_views',
                    'cont_image'
                ]
            );

            //5 bài mới nhất
            $arrContentNews = $instanceSearchContent->getListLimit(
                [
                    'cont_status' => 1,
                    'not_cont_id' => $arrContent['cont_id']],
                1,
                6,
                ['created_date' => ['order' => 'desc']],
                [
                    'cont_title',
                    'cont_slug',
                    'cont_main_image',
                    'cont_description',
                    'cont_id',
                    'cate_id'
                ]
            );

            //lấy 10 keyword :)
            $instanceSearchKeyword = new \My\Search\Keyword();
            $arrKeywordList = $instanceSearchKeyword->getListLimit(
                [
                    'full_text_keyname' => $arrContent['cont_title']
                ],
                1,
                10,
                ['_score' => ['order' => 'desc']],
                [
                    'key_id',
                    'key_name',
                    'key_slug'
                ]
            );

            //LẤY LIST TAG
            $arrTagList = [];
            if (!empty($arrContent['tag_id'])) {
                $instanceSearchTag = new \My\Search\Tag();
                $arrTagList = $instanceSearchTag->getList(
                    [
                        'in_tag_id' => explode(',', $arrContent['tag_id']),
                        'tag_status' => 1
                    ],
                    [],
                    [
                        'tag_id',
                        'tag_name',
                        'tag_slug'
                    ]
                );
            }

            unset($serviceContent, $instanceSearchTag, $instanceSearchContent, $instanceSearchKeyword, $arrConditionContent);
            return array(
                'params' => $params,
                'arrContent' => $arrContent,
                'arrCategoryDetail' => $arrCategoryDetail,
                'arrContentLikeList' => $arrContentLikeList,
                'arrContentLastedList' => $arrContentLastedList,
                'arrContentNews' => $arrContentNews,
                'arrKeywordList' => $arrKeywordList,
                'metaTitle' => $metaTitle,
                'metaDescription' => $metaDescription,
                'arrTagList' => $arrTagList,
                'cont_detail_image' => $cont_detail_image
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