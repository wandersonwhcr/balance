<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
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
        // Camada de Modelo
        $pPostings = $this->getServiceLocator()->get('Balance\Model\Balance');
        // Parâmetros de Execução
        $params = $this->params()->fromQuery();
        // Consulta de Balancete
        $elements = $pPostings->fetch(new Parameters($params));
        // Camada de Visualização
        return new ViewModel(array(
            'elements' => $elements,
        ));
    }
}
