<?php

namespace My\StaticManager;

class StaticManager
{

    private $serviceLocator;
    private $arrData = ['js' => [], 'css' => []];
    private $strResource = '';

    /**
     *
     * @param String $strResource
     */
    public function __construct($strResource = '', $serviceLocator)
    {

        if (empty($strResource) || !$serviceLocator instanceof \Zend\ServiceManager\ServiceLocatorInterface) {
            throw new \Exception('resource cannot be blank and $serviceLocator must be instance of Zend\ServiceManager\ServiceLocatorInterface');
        }

        $this->strResource = $strResource;
        $this->serviceLocator = $serviceLocator;
        $extension_js = APPLICATION_ENV === 'development' ? '.debug' : '';
        $extension_css = APPLICATION_ENV === 'development' ? '' : '.min';

        list($strModule, $strController, $strAction) = explode(':', $strResource);
        switch ($strModule) {
            case 'frontend':
                $str_js = 'library/jquery.min.js,my/static' . $extension_js . '.js';
                $str_css = 'fonts' . $extension_css . '.css,styleb2d3' . $extension_css . '.css,mobile9efa' . $extension_css . '.css';
                switch ($strController) {
                    case 'index' :
                        $str_js .= ',my/index' . $extension_js . '.js';
                        break;
                    case 'category':
                        $str_js .= ',library/slick-1.6.0/slick' . $extension_js . '.js,my/category' . $extension_js . '.js';
                        $str_css .= ',library/slick-1.6.0/slick.css';
                        break;
                    case 'content':
                        $str_js .= ',library/videojs/video' . $extension_js . '.js,library/videojs/Youtube.min.js,my/post' . $extension_js . '.js';
                        $str_css .= ',library/videojs/videojs.css';
                        break;
                    case 'keyword':
                        break;
                    case 'category':
                        break;
                    default :
                        break;
                }
                $this->arrData['css']['defaultCSS'] = STATIC_URL . '/fe/css/??' . $str_css;
                $this->arrData['js']['defaultJS'] = STATIC_URL . '/fe/js/??' . $str_js;
                break;
            case 'backend':
                $this->arrData['css']['defaultCSS'] = STATIC_URL . '/b/css/??bootstrap.min.css,bootstrap-reset.css,font-awesome.css,style.css,style-responsive.css,datepicker.css';
                $this->arrData['js']['defaultJS'] = STATIC_URL . '/b/js/library/??jquery.js,bootstrap.min.js,jquery.dcjqaccordion.2.7.js,hover-dropdown.js,jquery.scrollTo.min.js,jquery.nicescroll.js,respond.min.js,common-scripts.js,jquery.knob.js,bootbox.min.js,bootstrap-datepicker.js,bootstrap-inputmask.min.js';
                break;
            default:
                $this->arrData['css']['defaultCSS'] = '';
                $this->arrData['js']['defaultJS'] = '';
                break;
        }
    }

    /**
     *
     * @param String|Array $arrData //ex : a.css | a.css,b.css | [a.css, b.css]
     * @return $this
     */
    public function setCSS($arrData = '')
    {
        if (!is_array($arrData) && $arrData) {
            $arrData = [$arrData];
        }
        if (is_array($arrData['defaultCSS'])) {
            $arrData = $arrData ? $arrData['defaultCSS'][$this->strResource] : '';
            $this->arrData['css']['defaultCSS'] .= $arrData ? ',' . $arrData : '';
        }
        if (is_array($arrData['externalCSS'])) {
            $arrCSS = is_array($arrData['externalCSS'][$this->strResource]) ? $arrData['externalCSS'][$this->strResource] : $arrData['externalCSS'];
            $this->arrData['css']['externalCSS'] = $arrCSS ? $arrCSS : [];
        }
        foreach ($arrData['externalCSS'] as $key => $link) {
            if (is_int($key)) {
                array_push($this->arrData['css']['externalCSS'], $link);
            }
        }
        return $this;
    }

    /**
     *
     * @param String|Array $data
     * @return $this
     */
    public function setJS($arrData = '')
    {
        if (!is_array($arrData) && $arrData) {
            $arrData = [$arrData];
        }

        if (is_array($arrData['defaultJS'])) {
            $arrData = $arrData ? $arrData['defaultJS'][$this->strResource] : '';
            $this->arrData['js']['defaultJS'] .= $arrData ? ',' . $arrData : '';
        }
        if (is_array($arrData['externalJS'])) {
            $arrJS = is_array($arrData['externalJS'][$this->strResource]) ? $arrData['externalJS'][$this->strResource] : $arrData['externalJS'];
            $this->arrData['js']['externalJS'] = $arrJS ? $arrJS : [];
        }
        foreach ($arrJS['externalJS'] as $key => $link) {
            if (is_int($key)) {
                $this->arrData['js']['externalJS'][] = $link;
            }
        }
        return $this;
    }

    public function render($version = '')
    {
        $version = $version && APPLICATION_ENV === 'production' ? $version : time();
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
        if ($this->arrData['css']['defaultCSS']) {
            $renderer->headLink()->offsetSetStylesheet(1, $this->arrData['css']['defaultCSS'] . '?v=' . $version);
        }
        if ($this->arrData['js']['defaultJS']) {
            $renderer->headScript()->setAllowArbitraryAttributes(true)->offsetSetFile(1, $this->arrData['js']['defaultJS'] . '?v' . $version, 'text/javascript');
        }
        if ($this->arrData['css']['externalCSS']) {
            $tmp = 1;
            foreach ($this->arrData['css']['externalCSS'] as $k => $css) {
                $tmp += 1;
                $renderer->headLink()->offsetSetStylesheet($tmp, $css . '?v=' . $version);
            }
        }
        if ($this->arrData['js']['externalJS']) {
            $tmp = 1;
            foreach ($this->arrData['js']['externalJS'] as $k => $arrData) {
                $tmp += 1;
                $renderer->headScript()->setAllowArbitraryAttributes(true)->offsetSetFile($tmp, $arrData . '?v' . $version, 'text/javascript');
            }
        }
    }

}
