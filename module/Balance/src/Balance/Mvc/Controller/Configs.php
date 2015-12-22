<?php

namespace Balance\Mvc\Controller;

use Exception;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Controladora de Configurações
 */
class Configs extends AbstractActionController
{
    /**
     * Captura de Localização para Javascript
     *
     * @return string Valor Solicitado
     */
    protected function getLocale()
    {
        return strtolower(str_replace('_', '-', locale_get_default()));
    }

    /**
     * Apresentar Configurações
     *
     * @return JsonModel Modelo de Visualização
     */
    public function jsAction()
    {
        // Capturar Configurações
        $configs = [];
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
        $view->setVariable('locale', $this->getLocale());
        // Configurar Variável
        $view->setJsonpCallback('$.application.setConfigs');
        // Apresentação
        return $view;
    }

    /**
     * Configurações de Módulo
     *
     * @return ViewModel
     */
    public function modulesAction()
    {
        // Camada de Modelo
        $mModules = $this->getServiceLocator()->get('Balance\Model\Modules');
        // Consultar Informações
        $elements = $mModules->fetch(new Parameters());
        // Camada de Visualização
        return new ViewModel([
            'elements' => $elements,
        ]);
    }
}
