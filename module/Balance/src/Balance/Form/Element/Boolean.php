<?php

namespace Balance\Form\Element;

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
            $this->setValueOptions(array(
                'true'  => 'Sim',
                'false' => 'Não',
            ));
            // Captura
            $valueOptions = parent::getValueOptions();
        }
        return $valueOptions;
    }
}
