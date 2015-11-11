<?php

namespace Balance\Controller;

use Balance\Model\Model;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controladora de Contas
 */
class Accounts extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    // Traits
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    // Traits de Ação
    use IndexActionTrait;
    use EditActionTrait;
    use RemoveActionTrait;
}
