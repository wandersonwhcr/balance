<?php

namespace Balance\Mvc\Controller;

use Balance\Model\Model;

/**
 * Trait para Configuração de Camada de Modelo
 */
trait ModelAwareTrait
{
    /**
     * Camada de Modelo
     * @type Model
     */
    private $model;

    /**
     * Configuração de Camada de Modelo
     *
     * @param  Model           $model Elemento para Configuração
     * @return ModelAwareTrait Próprio Objeto para Encadeamento
     */
    public function setModel(Model $model)
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
}
