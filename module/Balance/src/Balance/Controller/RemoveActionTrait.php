<?php

namespace Balance\Controller;

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
