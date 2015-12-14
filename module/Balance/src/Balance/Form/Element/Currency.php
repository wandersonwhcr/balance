<?php

namespace Balance\Form\Element;

use NumberFormatter;
use Zend\Form\Element\Text;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Elemento de Formulário para Valores Monetários
 */
class Currency extends Text implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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
            array_unshift($classes, 'form-control-currency');
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
        if ($option === 'add-on-prepend' && ! $value) {
            // Valor Padrão
            $value = (new NumberFormatter(null, NumberFormatter::CURRENCY))
                ->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
            // Configuração
            $this->setOption($option, $value);
        }
        // Apresentação
        return $value;
    }
}
