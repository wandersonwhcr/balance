<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Balance\Mvc\Controller\RemoveActionTrait;
use Zend\Mvc\Controller\AbstractActionController;

class RemoveActionWithoutRedirectRouteNameController extends AbstractActionController implements ModelAwareInterface
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use RemoveActionTrait;
}
