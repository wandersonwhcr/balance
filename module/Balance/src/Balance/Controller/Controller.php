<?php

namespace Balance\Controller;

use Balance\Model\Model;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controladora
 */
class Controller extends AbstractActionController
{
    // Traits
    use IndexActionTrait;
    use EditActionTrait;
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
}
