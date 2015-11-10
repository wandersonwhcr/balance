<?php

namespace Balance\Controller;

use Balance\Model\Model;
use Balance\Model\ModelException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;

/**
 * Controladora
 */
class Controller extends AbstractActionController
{
    // Traits
    use IndexActionTrait;
    use RemoveActionTrait;

    /**
     * Camada de Modelo
     * @type Model
     */
    private $model;

    /**
     * Nome da Rota para Redirecionamento
     * @type string
     */
    private $redirectRouteName;

    /**
     * Construtor Padrão
     *
     * @param Model  $model             Camada de Modelo
     * @param string $redirectRouteName Nome da Rota para Redirecionamento
     */
    public function __construct(Model $model, $redirectRouteName)
    {
        $this
            ->setModel($model)
            ->setRedirectRouteName($redirectRouteName);
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
     * Configuração do Nome da Rota para Redirecionamento
     *
     * @param  string     $redirectRouteName Valor para Configuração
     * @return Controller Próprio Objeto para Encadeamento
     */
    public function setRedirectRouteName($redirectRouteName)
    {
        $this->redirectRouteName = $redirectRouteName;
        return $this;
    }

    /**
     * Apresentação do Nome da Rota para Redirecionamento
     *
     * @return string Valor Configurado
     */
    public function getRedirectRouteName()
    {
        return $this->redirectRouteName;
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
        return new ViewModel(array(
            'type' => ($params ? 'edit' : 'add'),
            'form' => $model->getForm(),
        ));
    }
}
