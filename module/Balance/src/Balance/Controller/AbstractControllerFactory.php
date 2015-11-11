<?php

namespace Balance\Controller;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Construtor de Camada de Controle
 */
class AbstractControllerFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Captura de Configuração
        $config = $serviceLocator->getServiceLocator()->get('Config')['balance_manager']['factories'];
        // Configurado e Parâmetros Corretos?
        return
            isset($config[$requestedName])
            && isset($config[$requestedName]['factory'])
            && $config[$requestedName]['factory'] === __CLASS__
            && isset($config[$requestedName]['params'])
            && isset($config[$requestedName]['params']['model'])
            && isset($config[$requestedName]['params']['redirect_route_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Localizador Superior
        $parentServiceLocator = $serviceLocator->getServiceLocator();
        // Captura de Configuração
        $config = $parentServiceLocator->get('Config')['balance_manager']['factories'][$requestedName];

        // Inicialização
        $controller = new Controller();

        // Camada de Modelo?
        if ($controller instanceof ModelAwareInterface) {
            // Solicitar Camada de Modelo
            $model = $parentServiceLocator->get($config['params']['model']);
            // Configuração
            $controller->setModel($model);
        }

        // Rota para Redirecionamento?
        if ($controller instanceof RedirectRouteNameAwareInterface) {
            // Captura de Rota para Redirecionamento
            $redirectRouteName = $config['params']['redirect_route_name'];
            // Configuração
            $controller->setRedirectRouteName($redirectRouteName);
        }

        // Apresentação
        return $controller;
    }
}
