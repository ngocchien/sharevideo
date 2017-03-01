<?php

namespace My\Controller;

use Zend\Mvc\MvcEvent,
    Zend\Mvc\Controller\AbstractActionController,
    My\General;

class MyController extends AbstractActionController
{

    protected $defaultJS = '';
    protected $externalJS = '';
    protected $defaultCSS = '';
    protected $externalCSS = '';
    protected $serverUrl;
    protected $authservice;
    private $resource;
    private $renderer;

    public function onDispatch(MvcEvent $e)
    {
        if (php_sapi_name() != 'cli') {
            $this->serverUrl = $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getHost();
            $this->params = array_merge($this->params()->fromRoute(), $this->params()->fromQuery());
            $this->params['module'] = strtolower($this->params['module']);
            $this->params['controller'] = strtolower($this->params['__CONTROLLER__']);
            $this->params['action'] = strtolower($this->params['action']);
            $this->resource = $this->params['module'] . ':' . $this->params['controller'] . ':' . $this->params['action'];
            $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
            $auth = $this->authenticate($this->params);

            if ($this->params['module'] === 'backend' && !$auth) {
                if (!$this->permission($this->params)) {
                    if ($this->request->isXmlHttpRequest()) {
                        die('Permission Denied!!!');
                    }
                    $this->layout('backend/error/accessDeny');
                    return false;
                }
            }

            $instanceStaticManager = new \My\StaticManager\StaticManager($this->resource, $this->serviceLocator);
            $instanceStaticManager
                ->setJS(array('defaultJS' => $this->defaultJS))
                ->setJS(array('externalJS' => $this->externalJS))
                ->setCSS(array('defaultCSS' => $this->defaultCSS))
                ->setCSS(array('externalCSS' => $this->externalCSS))
                ->render(2.1);
            $this->setMeta($this->params);
        }
        return parent::onDispatch($e);
    }

    private function setMeta($arrData)
    {
        if ($arrData['module'] == 'frontend') {
            //set all page
            $this->renderer->headMeta()
                ->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8')
                ->appendHttpEquiv('content-language', 'en-US')
                ->appendName('viewport', html_entity_decode('width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes'))
                ->appendName('author', html_entity_decode(\My\General::SITE_AUTH))
                ->appendName('robots', html_entity_decode('index, follow'))
                ->appendName('theme-color', html_entity_decode('#007cdb'));
            $this->renderer
                ->headLink(array('rel' => 'shortcut ', 'href' => STATIC_URL . '/images/favicon.png'))
                ->headLink(array('rel' => 'icon ', 'sizes' => '192x192', 'href' => STATIC_URL . '/images/favicon-192x192.png'));

            //set 1 page
            switch ($this->resource) {
                case 'frontend:index:index':
                    $this->renderer->headTitle(\My\General::SITE_DOMAIN . ' - ' . \My\General::SITE_SLOGAN);
                    $this->renderer->headMeta()
                        ->setProperty('url', \My\General::SITE_DOMAIN_FULL)
                        ->setProperty('og:url', \My\General::SITE_DOMAIN_FULL)
                        ->appendName('title', html_entity_decode(\My\General::SITE_DOMAIN . ' -' . General::SITE_SLOGAN))
                        ->setProperty('og:title', html_entity_decode(\My\General::SITE_DOMAIN . ' -' . General::SITE_SLOGAN))
                        ->setProperty('og:description', html_entity_decode(\My\General::DESCRIPTION_DEFAULT))
                        ->appendName('keywords', \My\General::KEYWORD_DEFAULT)
                        ->appendName('description', \My\General::DESCRIPTION_DEFAULT)
                        ->appendName('image', \My\General::SITE_IMAGES_DEFAULT)
                        ->setProperty('og:image', \My\General::SITE_IMAGES_DEFAULT);
                    $this->renderer
                        ->headLink(array('rel' => 'image_src', 'href' => \My\General::SITE_IMAGES_DEFAULT))
                        ->headLink(array('rel' => 'amphtml', 'href' => \My\General::SITE_DOMAIN_FULL))
                        ->headLink(array('rel' => 'canonical', 'href' => \My\General::SITE_DOMAIN_FULL));
                    break;
                default:
                    break;
            }
        }

        if ($arrData['module'] === 'backend') {
            $this->renderer->headTitle('Administrator - ' . \My\General::SITE_AUTH);
        }
    }

    private function permission($params)
    {

//check can access CPanel
        if (IS_ACP != 1) {
            return false;
        }

//check use in fullaccess role
        if (FULL_ACCESS) {
            return true;
        }

        $ser = $this->serviceLocator;
        $serviceACL = $this->serviceLocator->get('ACL');

        $strActionName = $params['action'];

        if (strpos($params['action'], '-')) {
            $strActionName = '';
            $arrActionName = explode('-', $params['action']);
            foreach ($arrActionName as $k => $str) {
                if ($k > 0) {
                    $strActionName .= ucfirst($str);
                }
            }
            $strActionName = $arrActionName[0] . $strActionName;
        }

        $strControllerName = $params['controller'];
        if (strpos($params['controller'], '-')) {
            $strControllerName = '';
            $arrControllerName = explode('-', $params['controller']);
            foreach ($arrControllerName as $k => $str) {
                if ($k > 0) {
                    $strControllerName .= ucfirst($str);
                }
            }
            $strControllerName = $arrControllerName[0] . $strControllerName;
        }

        $strActionName = str_replace('_', '', $strActionName);
        $strControllerName = str_replace('_', '', $strControllerName);

        return $serviceACL->checkPermission($params['module'], $strControllerName, $strActionName);
    }

    protected function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }

    private function authenticate($arrData)
    {
        define('MODULE', $arrData['module']);
        define('CONTROLLER', $arrData['controller']);
        define('ACTION', $arrData['action']);

        $arrUserData = $this->getAuthService()->getIdentity();
        if ($arrData['module'] === 'backend') {
            if (empty($arrUserData)) {
                return $this->redirect()->toRoute('backend', array('controller' => 'auth', 'action' => 'login'));
            }

            define('UID', (int)$arrUserData['user_id']);
            define('FULLNAME', $arrUserData['user_fullname']);
            define('USERNAME', $arrUserData['user_name']);
            define('EMAIL', $arrUserData['user_email']);
            define('GROU_ID', $arrUserData['group_id'] ? (int)$arrUserData['group_id'] : 0);
            define('IS_ACP', (empty($arrUserData['group_id']) ? 0 : $arrUserData['is_acp']));
            define('PERMISSION', json_encode($arrUserData['permission']));
            define('FULL_ACCESS', empty($arrUserData['is_full_access']) ? 0 : 1);
        }

        if ($arrData['module'] === 'frontend') {
            $instanceSearchCategory = new \My\Search\Category();
            $arrCategoryList = $instanceSearchCategory->getList(
                ['cate_status' => 1],
                [
                    'cate_id',
                    'cate_name',
                    'cate_slug',
                    'cate_sort',
                    'cate_meta_title',
                    'cate_meta_keyword',
                    'cate_meta_description',
                    'cate_description',
                    'parent_id',
                    'cate_img_url'
                ],
                ['cate_sort' => ['order' => 'asc'], 'cate_id' => ['order' => 'asc']]
            );
            $arrCategoryFormat = [];

            if (!empty($arrCategoryList)) {
                foreach ($arrCategoryList as $arrCategory) {
                    $arrCategoryFormat[$arrCategory['cate_id']] = $arrCategory;
                }
            }
            define('ARR_CATEGORY', serialize($arrCategoryFormat));

            //lấy 15 Tag mới nhất
            $instanceSearchTag = new \My\Search\Tag();
            $arrTag = $instanceSearchTag->getListLimit(
                ['tag_status' => 1],
                1,
                15,
                ['tag_id' => ['order' => 'desc']],
                [
                    'tag_name',
                    'tag_slug',
                    'tag_id'
                ]
            );
            define('ARR_TAG', serialize($arrTag));

            //get list content hot
//            $instanceSearchContent = new \My\Search\Content();
//            $arr_content_hot = $instanceSearchContent->getListLimit(
//                ['cont_status' => 1],
//                1,
//                10,
//                ['cont_views' => ['order' => 'desc']],
//                [
//                    'cont_title',
//                    'cont_slug',
//                    'cont_main_image',
//                    'cont_description',
//                    'cont_id'
//                ]
//            );
//            define('ARR_CONTENT_HOT_LIST', serialize($arr_content_hot));


            define('KEYWORD_SEARCH', !empty($arrData['keyword']) && $arrData['action'] == 'index' && $arrData['controller'] == 'search' ? $arrData['keyword'] : NULL);

            unset($arrKeywordList, $arr_content_hot, $instanceSearchCategory, $arrCategory, $arrCategoryParentList, $arrCategoryByParent, $arrCategoryFormat);
        }
    }

}
