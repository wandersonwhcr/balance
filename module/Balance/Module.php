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
        // Tradução
        $translator = $event->getApplication()->getServiceManager()->get('MvcTranslator');
        // Configuração
        $translator
            ->setLocale('pt_BR')
            ->addTranslationFile(
                'phpArray',
                './vendor/zendframework/zendframework/resources/languages/pt_BR/Zend_Validate.php',
                'default',
                'pt_BR'
            );
        call_user_func(array('Zend\Validator\AbstractValidator', 'setDefaultTranslator'), $translator);
    }
}
