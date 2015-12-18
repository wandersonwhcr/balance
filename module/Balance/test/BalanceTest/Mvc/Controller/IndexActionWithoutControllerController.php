<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\IndexActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class IndexActionWithoutControllerController implements ModelAwareInterface, ServiceLocatorAwareInterface
{
    use IndexActionTrait;
    use ModelAwareTrait;
    use ServiceLocatorAwareTrait;
}
