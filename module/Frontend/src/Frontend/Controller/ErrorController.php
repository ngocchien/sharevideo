<?php

namespace Frontend\Controller;

use My\Controller\MyController;

class ErrorController extends MyController
{
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */

    public function __construct()
    {
    }

    public function indexAction()
    {
    }


    public function e404Action()
    {
        $this->layout('layout/empty');
    }

}
