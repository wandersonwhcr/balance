<?php

namespace Balance;

/**
 * Inicialização do Módulo
 */
class Module
{
    /**
     * Carregar Configurações
     *
     * @return array Conteúdo Solicitado
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($event)
    {
        // Gerenciador de Serviços
        $serviceManager = $event->getApplication()->getServiceManager();

        // Tradução
        $translator = $serviceManager->get('MvcTranslator');
        // Configuração
        $translator
            ->setLocale(locale_get_default())
            ->addTranslationFilePattern(
                'phpArray',
                call_user_func(['Zend\I18n\Translator\Resources', 'getBasePath']),
                call_user_func(['Zend\I18n\Translator\Resources', 'getPatternForValidator'])
            );
        call_user_func(['Zend\Validator\AbstractValidator', 'setDefaultTranslator'], $translator);

        // Banco de Dados e Time Zone
        $serviceManager->get('db')
            ->query(sprintf("SET TIME ZONE '%s'", date_default_timezone_get()))
            ->execute();
    }
}
