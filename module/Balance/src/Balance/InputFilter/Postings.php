<?php

namespace Balance\InputFilter;

use Balance\I18n;
use Balance\Model\EntryType;
use Balance\Model\Persistence\ValueOptionsInterface;
use Balance\Posting\Checker;
use IntlDateFormatter;
use NumberFormatter;
use Zend\Filter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
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
            throw new InputFilterException('Invalid Model');
        }

        // Chave Primária
        $input = new Input();
        $input->getFilterChain()
            ->attach(new Filter\ToInt());
        $this->add($input, 'id');

        // Data e Hora
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new I18n\Validator\DateTime([
                'dateType' => IntlDateFormatter::MEDIUM,
                'timeType' => IntlDateFormatter::MEDIUM,
            ]));
        $this->add($input, 'datetime');

        // Descrição
        $this->add(new Input(), 'description');

        // Filtro: Entradas
        $filter = new InputFilter();

        // Entradas: Tipo
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\InArray(['haystack' => array_keys((new EntryType())->getDefinition())]));
        $filter->add($input, 'type');

        // Capturar Todas as Possíveis Entradas de Contas
        $options = [];
        foreach ($pAccounts->getValueOptions() as $identifier => $option) {
            if (is_array($option)) {
                $options = array_merge($options, array_keys($option['options']));
            } else {
                $options = array_merge($options, [$identifier]);
            }
        }

        // Entradas: Conta
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\InArray(['haystack' => $options]))
            ->attach(new Validator\Callback([
                'callback' => [$this, 'doValidateAccountId'],
                'message'  => 'O valor de entrada foi configurado em outra entrada de lançamento',
            ]));
        $input->getFilterChain()
            ->attach(new Filter\ToInt());
        $filter->add($input, 'account_id');

        // Entradas: Valor
        $input = new Input();
        $input->getValidatorChain()
            ->attach(new Validator\Regex([
                'pattern' => '/^[1-9]*[0-9]+,[0-9]{2}$/',
                'message' => 'O valor informado não está no formato esperado',
            ]))
            ->attach(new Validator\Callback([
                'callback' => [$this, 'doValidateValue'],
                'message'  => 'O valor de entrada não está balanceado',
            ]));
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
     * @return bool   Confirmação do Validador
     */
    public function doValidateAccountId($value)
    {
        // Inicialização
        $accounts = [];
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

    /**
     * Validação de Valores
     *
     * @param  string $value Valor para Verificação
     * @return bool   Confirmação do Validador
     */
    public function doValidateValue($value)
    {
        // Inicialização
        $checker   = new Checker();
        $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);
        // Configuração de Símbolo
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        // Tipos e Valores
        if (isset($this->data['entries']) && is_array($this->data['entries'])) {
            foreach ($this->data['entries'] as $entry) {
                if (is_array($entry) && isset($entry['type']) && isset($entry['value'])) {
                    // Capturar Valor Monetário
                    $value = $formatter->parseCurrency($entry['value'], $currency);
                    // Limpeza PHPMD
                    unset($currency);
                    // Adicionar Entrada
                    switch ($entry['type']) {
                        case Checker::CREDIT:
                        case Checker::DEBIT:
                            $checker->addValue($entry['type'], $value);
                            break;
                    }
                }
            }
        }
        // Validação
        return $checker->isValid();
    }
}
