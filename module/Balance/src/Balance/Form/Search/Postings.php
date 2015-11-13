<?php

namespace Balance\Form\Search;

use Balance\Form\FormException;
use Balance\Model\Persistence\ValueOptionsInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

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
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'keywords',
            'options' => array(
                'label' => 'Palavras-Chave',
            ),
        ));

        // Conta
        $this->add(array(
            'type'    => 'Select',
            'name'    => 'account_id',
            'options' => array(
                'label'         => 'Conta',
                'value_options' => $pAccounts->getValueOptions(),
            ),
        ));

        // Data e Hora Inicial
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'datetime_begin',
            'options' => array(
                'label' => 'Data e Hora Inicial',
            ),
        ));

        // Data e Hora Final
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'datetime_end',
            'options' => array(
                'label' => 'Data e Hora Final',
            ),
        ));
    }
}
