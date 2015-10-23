<?php

namespace Balance\Posting;

use InvalidArgumentException;

class Checker
{
    const CREDIT = 'CREDIT';
    const DEBIT  = 'DEBIT';

    protected $difference = 0.0;

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

    public function isValid()
    {
        return $this->difference === 0.0;
    }

    public function getDifference()
    {
        return $this->difference;
    }
}
