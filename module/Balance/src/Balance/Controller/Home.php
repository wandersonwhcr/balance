<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Controladora Principal
 */
class Home extends AbstractActionController
{
    /**
     * Ação Principal
     *
     * @return ViewModel Modelo de Visualização
     */
    public function indexAction()
    {
        throw new \Exception('Teste', 500, new \Exception('Anterior', 500, new \Exception('Mais Anterior')));
        $this->flashMessenger()
            ->addMessage('alert alert-success', 'success')
            ->addMessage('alert alert-info', 'info')
            ->addMessage('alert alert-warning', 'warning')
            ->addMessage('alert alert-danger', 'danger');
        return new ViewModel();
    }
}
