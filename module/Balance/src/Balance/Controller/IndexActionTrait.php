<?php

namespace Balance\Controller;

use Exception;
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
        // Camada de Modelo?
        if (! $this instanceof ModelAwareInterface) {
            // Erro Encontrado
            throw new Exception('Invalid Controller');
        }
        // Camada de Modelo
        $model = $this->getModel();
        // Parâmetros de Consulta
        $params = $this->getRequest()->getQuery();
        // Captura de Página
        $params['page'] = $this->params()->fromRoute('page');
        // Consulta de Elementos
        $elements = $model->fetch($params);
        // Utilizar Template do Roteamento
        $this->getServiceLocator()->get('ViewManager')
            ->getInjectTemplateListener()->setPreferRouteMatchController(true);
        // Camada de Visualização
        return new ViewModel(array(
            'elements' => $elements,
            'form'     => $model->getFormSearch(),
        ));
    }
}
