<?php

namespace Balance\Posting;

use InvalidArgumentException;

/**
 * Verificador de Lançamentos Balanceados
 */
class Checker
{
    const CREDIT = 'CREDIT';
    const DEBIT  = 'DEBIT';

    /**
     * Diferença Encontrada
     * @var float
     */
    protected $difference = 0.0;

    /**
     * Adiciona um Valor ao Verificador
     *
     * @param  string  $type  Tipo de Valor (Crédito ou Débito)
     * @param  float   $value Valor para Adição
     * @return Checker Próprio Objeto para Encadeamento
     */
    public function addValue($type, $value)
    {
        // Pesquisa
        switch ($type) {
            case self::CREDIT:
                // Configuração
                $this->difference = (float) bcadd($this->difference, $value, 2);
                break;
            case self::DEBIT:
                // Configuração
                $this->difference = (float) bcsub($this->difference, $value, 2);
                break;
            default:
                throw new InvalidArgumentException("Invalid Type: '$type'");
        }
        // Encadeamento
        return $this;
    }

    /**
     * Verificador Válido?
     *
     * Efetua a verificação se todos os valores adicionados estão balanceados entre crédito e débito, retornando verdade
     * se a diferença encontrada é igual a zero.
     *
     * @return bool Confirmação Solicitada
     */
    public function isValid()
    {
        return $this->difference === 0.0;
    }

    /**
     * Apresenta a Diferença Encontrada
     *
     * @return float Valor Solicitado
     */
    public function getDifference()
    {
        return $this->difference;
    }
}
