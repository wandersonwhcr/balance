<?php

namespace Balance\Model;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Construtor de Camada de Modelo
 */
class AbstractModelFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Captura de Configuração
        $config = $serviceLocator->get('Config')['balance_manager']['factories'];
        // Configurado e Parâmetros Corretos?
        return
            isset($config[$requestedName])
            && isset($config[$requestedName]['factory'])
            && $config[$requestedName]['factory'] === __CLASS__
            && isset($config[$requestedName]['params'])
            && isset($config[$requestedName]['params']['form'])
            && isset($config[$requestedName]['params']['input_filter'])
            && isset($config[$requestedName]['params']['form_search'])
            && isset($config[$requestedName]['params']['input_filter_search'])
            && isset($config[$requestedName]['params']['persistence']);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Captura de Configuração
        $config = $serviceLocator->get('Config')['balance_manager']['factories'][$requestedName];
        // Inicialização
        $form              = $serviceLocator->get('FormElementManager')->get($config['params']['form']);
        $inputFilter       = $serviceLocator->get('InputFilterManager')->get($config['params']['input_filter']);
        $formSearch        = $serviceLocator->get('FormElementManager')->get($config['params']['form_search']);
        $inputFilterSearch = $serviceLocator->get('InputFilterManager')->get($config['params']['input_filter_search']);
        $persistence       = $serviceLocator->get($config['params']['persistence']);
        // Configurações
        $form->setInputFilter($inputFilter);
        $formSearch->setInputFilter($inputFilterSearch);
        // Apresentação
        return new Model($form, $formSearch, $persistence);
    }
}
