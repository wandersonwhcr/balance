<?php

namespace Balance\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Controladora de Configurações
 */
class Configs extends AbstractActionController
{
    /**
     * Apresentar Configurações
     *
     * @return JsonModel Modelo de Visualização
     */
    public function indexAction()
    {
        // Capturar Configurações
        $configs = array();
        // Inicialização
        $view = new JsonModel($configs);
        // Configurar Variável
        $view->setJsonpCallback('Application.setConfigs');
        // Apresentação
        return $view;
    }
}
