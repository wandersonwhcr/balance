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
}
