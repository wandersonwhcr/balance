<?php

namespace Balance\Form\Element;

use Balance\Model\BooleanType;

/**
 * Elemento de Formulário Booleano
 */
class Boolean extends Select
{
    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        $valueOptions = parent::getValueOptions();
        if (! $valueOptions) {
            // Inicialização
            $this->setValueOptions((new BooleanType())->getDefinition());
            // Captura
            $valueOptions = parent::getValueOptions();
        }
        return $valueOptions;
    }
}
