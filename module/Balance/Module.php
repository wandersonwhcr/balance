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
        $locale     = $event->getApplication()->getServiceManager()->get('i18n')->getLocale();
        // Configuração
        $translator
            ->setLocale($locale)
            ->addTranslationFile(
                'phpArray',
                './vendor/zendframework/zend-i18n-resources/languages/' . $locale . '/Zend_Validate.php',
                'default',
                $locale
            );
        call_user_func(array('Zend\Validator\AbstractValidator', 'setDefaultTranslator'), $translator);
    }
}
