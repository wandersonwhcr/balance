<?php

namespace Balance\Form\Search;

use Balance\Form\FormException;
use Balance\Model\Persistence\ValueOptionsInterface;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Formulário de Pesquisa de Lançamentos
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

        // Palavras-Chave
        $this->add([
            'type'    => 'Text',
            'name'    => 'keywords',
            'options' => [
                'label' => 'Palavras-Chave',
            ],
        ]);

        // Conta
        $this->add([
            'type'    => 'Select',
            'name'    => 'account_id',
            'options' => [
                'label'         => 'Conta',
                'value_options' => $pAccounts->getValueOptions(),
            ],
        ]);

        // Data e Hora Inicial
        $this->add([
            'type'    => 'DateTime',
            'name'    => 'datetime_begin',
            'options' => [
                'label' => 'Data e Hora Inicial',
            ],
        ]);

        // Data e Hora Final
        $this->add([
            'type'    => 'DateTime',
            'name'    => 'datetime_end',
            'options' => [
                'label' => 'Data e Hora Final',
            ],
        ]);
    }
}
