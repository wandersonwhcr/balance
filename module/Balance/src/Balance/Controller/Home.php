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
        $this->flashMessenger()
            ->addMessage('alert alert-success', 'success')
            ->addMessage('alert alert-info', 'info')
            ->addMessage('alert alert-warning', 'warning')
            ->addMessage('alert alert-danger', 'error');
        return new ViewModel();
    }
}
