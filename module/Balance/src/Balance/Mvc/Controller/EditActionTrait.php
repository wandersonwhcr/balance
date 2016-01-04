<?php

namespace Balance\Mvc\Controller;

use Balance\Model\ModelException;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

/**
 * Trait para Edição de Elementos
 */
trait EditActionTrait
{
    /**
     * Editar Elemento
     *
     * @return ViewModel Modelo de Visualização
     */
    public function editAction()
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
        $params = array_diff_key($params, array_flip(['controller', 'action']));
        // Dados Enviados?
        if ($this->getRequest()->isPost()) {
            // Captura de Dados
            $data = $this->getRequest()->getPost();
            // Tratamento
            try {
                // Salvar Dados
                $model->save($data);
                // Sucesso
                $this->flashMessenger()->addSuccessMessage('Os dados foram salvos com sucesso.');
                // Redirecionamento
                return $this->redirect()->toRoute($this->getRedirectRouteName());
            } catch (ModelException $e) {
                // Erro Encontrado
                $this->flashMessenger()->addWarningMessage('Verifique o preenchimento dos campos em destaque.');
            }
        } else {
            // Chave Primária?
            if ($params) {
                // Tratamento
                try {
                    // Carregar Elemento
                    $model->load(new Parameters($params));
                } catch (ModelException $e) {
                    // Erro Encontrado
                    $this->flashMessenger()->addErrorMessage('Impossível carregar os dados solicitados.');
                    // Redirecionamento
                    return $this->redirect()->toRoute($this->getRedirectRouteName());
                }
            }
        }
        // Utilizar Template do Roteamento
        $this->getServiceLocator()->get('ViewManager')
            ->getInjectTemplateListener()->setPreferRouteMatchController(true);
        // Visualização
        $viewModel = new ViewModel([
            'type' => ($params ? 'edit' : 'add'),
            'form' => $model->getForm(),
        ]);
        // Evento: Após Inicializaçã
        $this->getEventManager()->trigger('Balance\Mvc\Controller\EditAction::afterViewModel', $viewModel);
        // Apresentação
        return $viewModel;
    }
}
