<?php

namespace Balance\Mvc\Controller;

use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Trait para Listar Elementos
 */
trait IndexActionTrait
{
    /**
     * Listar Elementos
     *
     * @return ViewModel Modelo de Visualização
     */
    public function indexAction()
    {
        // Controladora?
        if (! $this instanceof AbstractActionController) {
            // Erro Encontrado
            throw new Exception('Invalid Controller');
        }
        // Camada de Modelo?
        if (! $this instanceof ModelAwareInterface) {
            // Erro Encontrado
            throw new Exception('Invalid Controller');
        }
        // Camada de Modelo
        $model = $this->getModel();
        // Parâmetros de Consulta
        $params = $this->getRequest()->getQuery();
        // Consulta de Elementos
        $elements = $model->fetch($params);
        // Utilizar Template do Roteamento
        $this->getServiceLocator()->get('ViewManager')
            ->getInjectTemplateListener()->setPreferRouteMatchController(true);
        // Camada de Visualização
        return new ViewModel(array(
            'elements' => $elements,
            'form'     => $model->getFormSearch(),
            'params'   => $params,
        ));
    }
}
