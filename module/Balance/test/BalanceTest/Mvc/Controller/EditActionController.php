<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\EditActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface as IModelAware;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface as IRedirectRouteNameAware;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class EditActionController extends AbstractActionController implements IModelAware, IRedirectRouteNameAware
{
    use EditActionTrait;
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
}
