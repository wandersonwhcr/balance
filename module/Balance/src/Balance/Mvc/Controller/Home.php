<?php

namespace Balance\Mvc\Controller;

use Exception;
use Zend\Http;
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
        // Camada de Modelo
        $mBalance = $this->getServiceLocator()->get('Balance\Model\Balance');
        // Requisição
        $request = $this->getRequest();
        // Requisição Correta?
        if (! $request instanceof Http\PhpEnvironment\Request) {
            throw new Exception('Invalid Request');
        }
        // Parâmetros de Execução
        $params = $request->getQuery();
        // Consulta de Balancete
        $elements = $mBalance->fetch($params);
        // Camada de Visualização
        return new ViewModel([
            'elements' => $elements,
            'form'     => $mBalance->getFormSearch(),
        ]);
    }
}
