<?php

namespace Balance\Form;

use Balance\Model\EntryType;
use Balance\Model\Persistence\ValueOptionsInterface;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

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
        $this->add([
            'type' => 'Hidden',
            'name' => 'id',
        ]);

        // Data e Hora
        $this->add([
            'type' => 'DateTime',
            'name' => 'datetime',
        ]);

        // Descrição
        $this->add([
            'type' => 'Textarea',
            'name' => 'description',
        ]);

        // Subformulário: Entrada de Lançamento
        $subform = $this->getServiceLocator()->get('Fieldset');

        // Tipo de Entrada
        $subform->add([
            'type'    => 'Select',
            'name'    => 'type',
            'options' => [
                'value_options' => (new EntryType())->getDefinition(),
            ],
        ]);

        // Conta
        $subform->add([
            'type'    => 'Select',
            'name'    => 'account_id',
            'options' => [
                'value_options' => $pAccounts->getValueOptions(),
            ],
        ]);

        // Valor
        $subform->add([
            'type' => 'Currency',
            'name' => 'value',
        ]);

        // Coleção de Entradas de Lançamentos
        $this->add([
            'type'    => 'Collection',
            'name'    => 'entries',
            'options' => [
                'target_element' => $subform,
                'allow_add'      => true,
                'count'          => 2,
            ],
        ]);
    }
}
