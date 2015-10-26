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
}
