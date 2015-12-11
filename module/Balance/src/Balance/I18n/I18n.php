<?php

namespace Balance\I18n;

use IntlDateFormatter;
use NumberFormatter;

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

    /**
     * Criar um Novo Objeto de Formatação de Números
     *
     * @param  int             $style Estilo Utilizado no Construtor
     * @return NumberFormatter Elemento Solicitado
     */
    public function createNumberFormatter($style)
    {
        // Apresentação
        return new NumberFormatter($this->getLocale(), $style);
    }

    /**
     * Criar um Novo Objeto de Formatação de Data e Hora
     *
     * @param  int               $datetype Tipo de Data
     * @param  int               $timetype Tipo de Hora
     * @return IntlDateFormatter Elemento Solicitado
     */
    public function createDateFormatter($datetype, $timetype)
    {
        return new IntlDateFormatter($this->getLocale(), $datetype, $timetype);
    }
}
