<?php

namespace Balance\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Trait para Configuração de Localizador de Serviços
 */
trait ServiceLocatorAwareTrait
{
    /**
     * Localizador de Serviços
     * @type ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Configuração de Localizador de Serviços
     *
     * @param  ServiceLocatorInterface  $serviceLocator Elemento para Configuração
     * @return ServiceLocatorAwareTrait Elemento Solicitado
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Apresentação de Localizador de Serviços
     *
     * @return ServiceLocatorInterface Elemento Solicitado
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
