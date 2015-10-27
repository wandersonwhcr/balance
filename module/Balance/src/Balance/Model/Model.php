<?php

namespace Balance\Model;

use Zend\Form\Form;

/**
 * Camada de Modelo
 *
 * Estrutura utilizada como padrão para processamento de informações apresentadas pela camada de controle durante o
 * fluxo de processamento do aplicativo. Centraliza a configuração de objetos de formulário e persistência de dados.
 */
class Model
{
    /**
     * Formulário
     * @type Form
     */
    private $form;

    /**
     * Configuração de Formulário
     *
     * @param  Form  $form Elemento para Configuração
     * @return Model Próprio Objeto para Encadeamento
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Apresentação de Formulário
     *
     * @return Form Elemento Solicitado
     */
    public function getForm()
    {
        return $this->form;
    }
}
