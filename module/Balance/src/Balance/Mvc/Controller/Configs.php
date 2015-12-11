<?php

namespace Balance\Mvc\Controller;

use Exception;
use Zend\Http;
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
        // Requisição
        $request = $this->getRequest();
        // Tipagem Correta?
        if (! $request instanceof Http\PhpEnvironment\Request) {
            throw new Exception('Invalid Request');
        }
        // Configurar Caminho Base
        $view->setVariable('basePath', $this->getRequest()->getBaseUrl());
        // Configurar Linguagem de Localização
        $view->setVariable('languageLocale', $this->getServiceLocator()->get('i18n')->getLanguageLocale());
        // Configurar Variável
        $view->setJsonpCallback('$.application.setConfigs');
        // Apresentação
        return $view;
    }
}
