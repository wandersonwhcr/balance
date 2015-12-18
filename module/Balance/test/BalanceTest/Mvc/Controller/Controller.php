<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;

class Controller extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
}
