<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\EditActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface as IModelAware;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface as IRedirectRouteNameAware;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface as IServiceLocatorAware;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class EditActionWithoutControllerController implements IModelAware, IRedirectRouteNameAware, IServiceLocatorAware
{
    use EditActionTrait;
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use ServiceLocatorAwareTrait;
}
