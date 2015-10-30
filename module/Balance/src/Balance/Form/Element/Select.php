<?php

namespace Balance\Form\Element;

use Zend\Form\Element\Select as ZendSelect;

/**
 * Seletor Especializado
 */
class Select extends ZendSelect
{
    /**
     * {@inheritdoc}
     */
    public function getEmptyOption()
    {
        $option = parent::getEmptyOption();
        if (!isset($option)) {
            $option = '-- Selecione --';
            $this->setEmptyOption($option);
        }
        return $option;
    }
}
