<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

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

    /**
     * Ordenar Contas
     *
     * @return JsonModel
     */
    public function orderAction()
    {
        // Apresentação
        return new JsonModel(array(
            array(
                'type'    => 'success',
                'message' => 'Ordenação de elementos efetuada com sucesso.',
                'payload' => array(),
            ),
        ));
    }
}
