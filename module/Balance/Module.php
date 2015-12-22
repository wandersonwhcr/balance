<?php

namespace Balance;

use Balance\Module\ModuleInterface;

/**
 * Inicialização do Módulo
 */
class Module implements ModuleInterface
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

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'Balance';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return 'Módulo Padrão';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return
            'Este módulo representa todos os recursos básicos do Balance, incluindo o gerenciamento de contas e'
            . ' lançamentos, bem como o cálculo do balance na página principal do sistema.';
    }
}
