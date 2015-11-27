<?php

namespace Balance\Controller;

use Balance\Model\ModelException;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;

/**
 * Trait para Remover Elementos
 */
trait RemoveActionTrait
{
    /**
     * Remover Elemento
     *
     * @return ViewModel Modelo de Visualização
     */
    public function removeAction()
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
        // Redirecionamento?
        if (! $this instanceof RedirectRouteNameAwareInterface) {
            // Erro Encontrado
            throw new Exception('Invalid Controller');
        }
        // Camada de Modelo
        $model = $this->getModel();
        // Chave Primária
        $params = $this->params()->fromRoute();
        // Remover Controladora e Ação
        $params = array_diff_key($params, array_flip(array('controller', 'action')));
        // Tratamento
        try {
            // Remover Elemento
            $model->remove(new Parameters($params));
            // Sucesso
            $this->flashMessenger()->addSuccessMessage('Os dados foram removidos com sucesso.');
        } catch (ModelException $e) {
            // Erro Encontrado
            $this->flashMessenger()->addErrorMessage('Impossível remover os dados solicitados.');
        }
        // Redirecionamento
        return $this->redirect()->toRoute($this->getRedirectRouteName());
    }
}
