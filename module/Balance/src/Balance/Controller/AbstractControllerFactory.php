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
            && isset($config[$requestedName]['params']['redirect_route']);
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
        // Solicitar Camada de Modelo
        $model = $parentServiceLocator->get($config['params']['model']);
        // Apresentação
        return new Controller($model);
    }
}
