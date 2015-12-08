<?php

namespace Balance\Test\Mvc;

use Zend\Mvc\Application as MvcApplication;

/**
 * Aplicativo MVC para Testes
 */
final class Application
{
    /**
     * Instância de Aplicativo para Acesso
     * @var MvcApplication
     */
    private static $application;

    /**
     * Configuração de Instância de Aplicativo
     *
     * @param MvcApplication $application Instância para Configuração
     */
    public static function setApplication(MvcApplication $application)
    {
        self::$application = $application;
    }

    /**
     * Apresentação de Instância de Aplicativo
     *
     * @return MvcApplication Instância Configurada
     */
    public static function getApplication()
    {
        return self::$application;
    }
}
