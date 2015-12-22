<?php

namespace Balance\Model;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo para Módulos
 */
class Modules implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Consulta de Módulos
     *
     * Responsável pela consulta de módulos disponíveis, estejam eles instalados ou não. Apresenta o identificador do
     * módulo, título, descrição, um estado informando se está instalado ou não e outro se ele é padrão do sistema. As
     * informações dos módulos são retornadas na ordem em que devem ser apresentados. Ainda existe a possibilidade de
     * filtrar módulos instalados ou não instalados.
     *
     * @param  Parameters  $params Parâmetros de Consulta
     * @return Traversable Conjunto de Elementos Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Apresentação
        return $this->getServiceLocator()->get('Balance\Model\Persistence\Modules')->fetch($params);
    }
}
