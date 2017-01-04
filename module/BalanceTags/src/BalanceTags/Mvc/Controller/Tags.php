<?php

namespace BalanceTags\Mvc\Controller;

use Balance\Mvc\Controller\EditActionTrait;
use Balance\Mvc\Controller\IndexActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use Balance\Mvc\Controller\RemoveActionTrait;
use Zend\Mvc\Controller\AbstractActionController;


/**
 * Controladora de Etiquetas
 */
class Tags extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    // Traits
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    // Traits de Ação
    use IndexActionTrait;
    use EditActionTrait;
    use RemoveActionTrait;
}
