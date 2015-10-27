<?php

namespace Balance\Controller;

use Balance\Model\ModelException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

/**
 * Controladora de Contas
 */
class Accounts extends AbstractActionController
{
    /**
     * Ação Principal
     *
     * @return ViewModel Modelo de Visualização
     */
    public function indexAction()
    {
        // Camada de Modelo
        $model = $this->getServiceLocator()->get('Balance\Model\Accounts');
        // Parâmetros de Consulta
        $params = $this->getRequest()->getPost();
        // Consulta de Elementos
        $elements = $model->fetch($params);
        // Camada de Visualização
        return new ViewModel(array(
            'elements' => $elements,
        ));
    }

    /**
     * Editar Elemento
     *
     * @return ViewModel Modelo de Visualização
     */
    public function editAction()
    {
        // Camada de Modelo
        $model = $this->getServiceLocator()->get('Balance\Model\Accounts');
        // Chave Primária
        $params = $this->params()->fromRoute();
        // Remover Controladora e Ação
        $params = array_diff_key($params, array_flip(array('controller', 'action')));
        // Dados Enviados?
        if ($this->getRequest()->isPost()) {
            // Captura de Dados
            $data = $this->getRequest()->getPost();
            // Tratamento
            try {
                // Salvar Dados
                $model->save($data);
                // Redirecionamento
                return $this->redirect()->toRoute('accounts');
            } catch (ModelException $e) {
                // Erro Encontrado
            }
        } else {
            // Chave Primária?
            if ($params) {
                // Carregar Elemento
                $model->load(new Parameters($params));
            }
        }
        // Visualização
        return new ViewModel(array(
            'type' => ($params ? 'edit' : 'add'),
            'form' => $model->getForm(),
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
