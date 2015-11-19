<?php

namespace Balance\Form\Element;

use Zend\Form\Element\Text;

/**
 * Elemento de Formulário para Data e Hora
 */
class DateTime extends Text
{
    /**
     * {@inheritdoc}
     */
    public function getAttribute($key)
    {
        // Captura
        $value = parent::getAttribute($key);
        // Parâmetro
        if ($key === 'class') {
            // Capturar Valor
            $classes = array_filter(array_map('trim', explode(' ', $value)));
            // Adicionar Classe
            array_unshift($classes, 'form-control-datetimepicker');
            // Melhorias
            $classes = array_unique($classes);
            // Aplicação
            $value = implode(' ', $classes);
            // Configuração
            $this->setAttribute($key, $value);
        }
        // Apresentação
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($option)
    {
        // Captura
        $value = parent::getOption($option);
        // Parâmetro?
        if ($option === 'add-on-append' && ! $value) {
            // Valor Padrão
            $value = '<span class="glyphicon glyphicon-calendar"></span>';
            // Configuração
            $this->setOption($option, $value);
        }
        // Apresentação
        return $value;
    }
}
