<?php

namespace My\Job;

use My\General;

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
        }
        return $service;
    }

}
