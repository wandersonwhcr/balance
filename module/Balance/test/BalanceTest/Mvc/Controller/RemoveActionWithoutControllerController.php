<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\ModelAwareInterface as IModelAware;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface as IRedirectRouteNameAware;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Balance\Mvc\Controller\RemoveActionTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface as IServiceLocatorAware;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class RemoveActionWithoutControllerController implements IModelAware, IRedirectRouteNameAware, IServiceLocatorAware
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use RemoveActionTrait;
    use ServiceLocatorAwareTrait;
}
