<?php

namespace Balance\Model;

use Traversable;
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
     * módulo, título, descrição, um estado informando se está instalado ou não. As informações dos módulos são
     * retornadas na ordem em que devem ser apresentados. Ainda existe a possibilidade de filtrar módulos instalados ou
     * não instalados.
     *
     * @param  Parameters  $params Parâmetros de Consulta
     * @return Traversable Conjunto de Elementos Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Apresentação
        $result = $this->getServiceLocator()->get('Balance\Model\Persistence\Modules')->fetch($params);
        // Tipagem Correta?
        if (! $result instanceof Traversable) {
            throw new ModelException('Persistence Result is not Traversable');
        }
        // Apresentação
        return $result;
    }
}
