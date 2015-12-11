<?php

namespace Balance\I18n;

/**
 */
class I18n
{
    /**
     * Localização
     *
     * @type string
     */
    private $locale;

    /**
     * Construtor Padrão
     *
     * @param string $locale Valor para Configuração
     */
    public function __construct($locale)
    {
        $this->setLocale($locale);
    }

    /**
     * Configuração de Localização
     *
     * @param  string $locale Valor para Configuração
     * @return I18n   Próprio Objeto para Encadeamento
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Apresentação de Localização
     *
     * @return string Valor Configurado
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
