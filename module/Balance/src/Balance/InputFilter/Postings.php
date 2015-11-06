<?php

namespace Balance\InputFilter;

use Balance\Model\EntryType;
use Balance\Model\Persistence\ValueOptionsInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Filter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Validator;

/**
 * Validação de Dados de Lançamento
 */
class Postings extends InputFilter implements ServiceLocatorAwareInterface
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
        $input = new Input();
        $input->getFilterChain()
            ->attach(new Filter\ToInt());
        $this->add($input, 'id');

        // Data e Hora
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\Date(array('format' => 'd/m/Y H:i:s')));
        $this->add($input, 'datetime');

        // Descrição
        $this->add(new Input(), 'description');

        // Filtro: Entradas
        $filter = new InputFilter();

        // Entradas: Tipo
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\InArray(array('haystack' => array_keys((new EntryType())->getDefinition()))));
        $filter->add($input, 'type');

        // Entradas: Conta
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\InArray(array('haystack' => array_keys($pAccounts->getValueOptions()))))
            ->attach(new Validator\Callback(array($this, 'doValidateAccountId')));
        $input->getFilterChain()
            ->attach(new Filter\ToInt());
        $filter->add($input, 'account_id');

        // Entradas: Valor
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\Regex(array('pattern' => '/^[1-9]*[0-9]+,[0-9]{2}$/')));
        $filter->add($input, 'value');

        // Coleção: Entradas
        $collection = (new CollectionInputFilter())
            ->setInputFilter($filter)
            ->setCount(2);
        $this->add($collection, 'entries');
    }

    /**
     * Validação de Contas
     *
     * @param  string $value Valor para Verificação
     * @param  array  $data  Dados Completos
     * @return bool   Confirmação do Validador
     */
    public function doValidateAccountId($value)
    {
        // Inicialização
        $accounts = array();
        // Entradas
        if (isset($this->data['entries']) && is_array($this->data['entries'])) {
            foreach ($this->data['entries'] as $entry) {
                if (is_array($entry) && isset($entry['account_id'])) {
                    $accounts[] = (int) $entry['account_id'];
                }
            }
        }
        // Contabilizar uma só Entrada!
        $counter = array_count_values($accounts);
        // Resultado
        return isset($counter[$value]) && $counter[$value] === 1;
    }
}
