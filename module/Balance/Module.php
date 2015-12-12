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
            ->addTranslationFile(
                'phpArray',
                './vendor/zendframework/zend-i18n-resources/languages/' . locale_get_default() . '/Zend_Validate.php',
                'default',
                locale_get_default()
            );
        call_user_func(array('Zend\Validator\AbstractValidator', 'setDefaultTranslator'), $translator);

        // Banco de Dados e Time Zone
        $serviceManager->get('db')
            ->query(sprintf("SET TIME ZONE '%s'", date_default_timezone_get()))
            ->execute();
    }
}
