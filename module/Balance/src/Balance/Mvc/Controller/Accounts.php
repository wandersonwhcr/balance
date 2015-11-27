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
        // Camada de Persistência
        $pAccounts = $this->getServiceLocator()->get('Balance\Model\Persistence\Accounts');
        // Solicitar Ordenação
        $pAccounts->order($this->getRequest()->getPost());
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
