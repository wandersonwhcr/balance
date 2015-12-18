<?php

namespace Balance\InputFilter\Search;

use Balance\I18n;
use IntlDateFormatter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * Filtro de Pesquisa para Contas
 */
class Balance extends InputFilter
{
    public function init()
    {
        // Data e Hora Final
        $input = (new Input('datetime'))
            ->setRequired(false);
        $input->getValidatorChain()
            ->attach(new I18n\Validator\DateTime([
                'dateType' => IntlDateFormatter::MEDIUM,
                'timeType' => IntlDateFormatter::MEDIUM,
            ]));
        $this->add($input);
    }
}
