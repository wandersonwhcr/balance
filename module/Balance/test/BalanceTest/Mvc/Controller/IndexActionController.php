<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\IndexActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class IndexActionController extends AbstractActionController implements ModelAwareInterface
{
    use IndexActionTrait;
    use ModelAwareTrait;
}
