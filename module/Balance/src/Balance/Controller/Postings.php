<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Controladora de Lançamentos
 */
class Postings extends AbstractActionController
{
    /**
     * Ação Principal
     *
     * @return ViewModel Modelo de Visualização
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Editar Elemento
     *
     * @return ViewModel Modelo de Visualização
     */
    public function editAction()
    {
        // Chave Primária
        $id = (int) $this->params()->fromRoute('id');
        // Visualização
        return new ViewModel(array(
            'type' => ($id ? 'edit' : 'add'),
        ));
    }

    /**
     * Remover Elemento
     *
     * @return ViewModel Modelo de Visualização
     */
    public function removeAction()
    {
        return new ViewModel();
    }
}
