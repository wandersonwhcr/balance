<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\EditActionTrait;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class EditActionWithoutModelController extends AbstractActionController implements RedirectRouteNameAwareInterface
{
    use ModelAwareTrait;
    use EditActionTrait;
    use RedirectRouteNameAwareTrait;
}
