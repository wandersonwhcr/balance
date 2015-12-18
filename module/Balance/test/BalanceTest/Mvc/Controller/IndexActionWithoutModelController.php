<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\IndexActionTrait;
use Balance\Mvc\Controller\ModelAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class IndexActionWithoutModelController extends AbstractActionController
{
    use IndexActionTrait;
    use ModelAwareTrait;
}
