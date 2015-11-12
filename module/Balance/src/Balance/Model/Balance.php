<?php

namespace Balance\Model;

use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo para Balancete
 */
class Balance implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Consultar Elementos
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Valores Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Consulta
        return $this->getServiceLocator()->get('Balance\Model\Persistence\Balance')->fetch($params);
    }
}
