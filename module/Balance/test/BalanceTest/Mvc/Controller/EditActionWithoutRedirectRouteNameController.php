<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\EditActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class EditActionWithoutRedirectRouteNameController extends AbstractActionController implements ModelAwareInterface
{
    use EditActionTrait;
    use ModelAwareTrait;
}
