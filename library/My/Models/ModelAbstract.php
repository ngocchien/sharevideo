<?php

namespace My\Models;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface;

class ModelAbstract implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function __construct()
    {

    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
