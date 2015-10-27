<?php

namespace Balance\Controller;

use Balance\Model\Model;
use Balance\Model\ModelException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

/**
 * Controladora
 */
class Controller extends AbstractActionController
{
    /**
     * Camada de Modelo
     * @type Model
     */
    private $model;

    /**
     * Construtor Padrão
     *
     * @param Model $model Camada de Modelo
     */
    public function __construct(Model $model)
    {
        $this->setModel($model);
    }

    /**
     * Configuração de Camada de Modelo
     *
     * @param  Model      $model Elemento para Configuração
     * @return Controller Próprio Objeto para Encadeamento
     */
    protected function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Apresentação de Camada de Modelo
     *
     * @return Model Elemento Solicitado
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Ação Principal
     *
     * @return ViewModel Modelo de Visualização
     */
    public function indexAction()
    {
        // Camada de Modelo
        $model = $this->getModel();
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
        $model = $this->getModel();
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
                // Tratamento
                try {
                    // Carregar Elemento
                    $model->load(new Parameters($params));
                } catch (ModelException $e) {
                    // Redirecionamento
                    return $this->redirect()->toRoute('accounts');
                }
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
        } catch (ModelException $e) {
            // Erro Encontrado
        }
        // Redirecionamento
        return $this->redirect()->toRoute('accounts');
    }
}
