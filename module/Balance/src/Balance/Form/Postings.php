<?php

namespace Balance\Form;

use Balance\Model\EntryType;
use Balance\Model\Persistence\ValueOptionsInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Formulário de Lançamentos
 */
class Postings extends Form implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Inicialização
        $pAccounts = $this->getServiceLocator()->getServiceLocator()->get('Balance\Model\Persistence\Accounts');

        // Verificações
        if (! $pAccounts instanceof ValueOptionsInterface) {
            throw new FormException('Invalid Model');
        }

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
        $subform = $this->getServiceLocator()->get('Fieldset');

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
                'value_options' => $pAccounts->getValueOptions(),
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
                'count'          => 2,
            ),
        ));
    }
}
