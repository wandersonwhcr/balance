<?php

namespace Balance\Db\TableGateway;

use Zend\Db;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Construtor de Camada de Banco de Dados
 */
class AbstractTableGatewayFactory implements AbstractFactoryInterface
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
            && $config[$requestedName]['factory'] == __CLASS__
            && isset($config[$requestedName]['params'])
            && isset($config[$requestedName]['params']['table']);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Captura de Configuração
        $config = $serviceLocator->get('Config')['balance_manager']['factories'][$requestedName];
        // Inicialização
        $table = new Db\TableGateway\TableGateway($config['params']['table'], $serviceLocator->get('db'));
        // Chave Primária e Sequência?
        if (isset($config['params']['primary_key']) && isset($config['params']['sequence'])) {
            // Configurar Sequência de Chave Primária
            $table->getFeatureSet()->addFeature(
                new Db\TableGateway\Feature\SequenceFeature(
                    $config['params']['primary_key'],
                    $config['params']['sequence']
                )
            );
        }
        // Apresentação
        return $table;
    }
}
