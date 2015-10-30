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
            && isset($config[$requestedName]['params']['search_form'])
            && isset($config[$requestedName]['params']['input_filter'])
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
        $form        = $serviceLocator->get('FormElementManager')->get($config['params']['form']);
        $searchForm  = $serviceLocator->get('FormElementManager')->get($config['params']['search_form']);
        $inputFilter = $serviceLocator->get('InputFilterManager')->get($config['params']['input_filter']);
        $persistence = $serviceLocator->get($config['params']['persistence']);
        // Configurações
        $form->setInputFilter($inputFilter);
        // Apresentação
        return new Model($form, $searchForm, $persistence);
    }
}
