<?php

namespace Balance\Mvc\Controller;

use Balance\Model\Model;

/**
 * Configuração de Camada de Modelo
 */
interface ModelAwareInterface
{
    /**
     * Configuração de Camada de Modelo
     *
     * @param  Model               $model Elemento para Configuração
     * @return ModelAwareInterface Próprio Objeto para Encadeamento
     */
    public function setModel(Model $model);

    /**
     * Apresentação de Camada de Modelo
     *
     * @return Model Elemento Solicitado
     */
    public function getModel();
}
