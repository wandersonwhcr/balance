<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controladora de Lançamentos
 */
class Postings extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    // Traits
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    // Traits de Ação
    use IndexActionTrait;
    use EditActionTrait;
    use RemoveActionTrait;
}
