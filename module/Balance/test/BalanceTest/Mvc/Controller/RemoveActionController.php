<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\ModelAwareInterface as IModelAware;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface as IRedirectRouteNameAware;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Balance\Mvc\Controller\RemoveActionTrait;
use Zend\Mvc\Controller\AbstractActionController;

class RemoveActionController extends AbstractActionController implements IModelAware, IRedirectRouteNameAware
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use RemoveActionTrait;
}
