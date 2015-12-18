<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Balance\Mvc\Controller\RemoveActionTrait;
use Zend\Mvc\Controller\AbstractActionController;

class RemoveActionWithoutModelController extends AbstractActionController implements RedirectRouteNameAwareInterface
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use RemoveActionTrait;
}
