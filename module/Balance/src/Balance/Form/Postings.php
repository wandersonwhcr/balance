<?php

namespace Balance\Form;

use Balance\Model\EntryType;
use Zend\Form\Fieldset;
use Zend\Form\Form;

/**
 * Formulário de Lançamentos
 */
class Postings extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Chave Primária
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));

        // Data e Hora
        $this->add(array(
            'type' => 'Text',
            'name' => 'datetime',
        ));

        // Descrição
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'description',
        ));

        // Subformulário: Entrada de Lançamento
        $subform = new Fieldset();

        // Tipo de Entrada
        $subform->add(array(
            'type'    => 'Select',
            'name'    => 'type',
            'options' => array(
                'value_options' => (new EntryType())->getDefinition(),
            ),
        ));

        // Conta
        $subform->add(array(
            'type'    => 'Select',
            'name'    => 'account_id',
            'options' => array(
                'value_options' => array(), // TODO
            ),
        ));

        // Valor
        $subform->add(array(
            'type' => 'Text',
            'name' => 'value',
        ));

        // Coleção de Entradas de Lançamentos
        $this->add(array(
            'type'    => 'Collection',
            'name'    => 'entries',
            'options' => array(
                'target_element' => $subform,
                'allow_add'      => true,
            ),
        ));
    }
}
