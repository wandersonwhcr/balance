<?php

namespace Balance\Model\Persistence\File;

use ArrayIterator;
use Balance\Module\ModuleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Camada de Persistência para Módu
 */
class Modules implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Apresentação de Elementos
     *
     * Captura informações do projeto, verificando quais os módulos que estão disponíveis e apresentando informações
     * sobre o mesmo. Módulos instalados e não instalados podem ser filtrados, bem como módulos pertencentes ao sistema.
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Elementos Solicitados
     */
    public function fetch(Parameters $params)
    {
        // Inicialização
        $result = [];
        // Gerenciador de Módulos
        $modules = $this->getServiceLocator()->get('ModuleManager')->getLoadedModules();
        // Captura
        foreach ($modules as $module) {
            // Tipagem Correta?
            if ($module instanceof ModuleInterface) {
                // Capturar Informações
                $result[] = [
                    'identifier'  => $module->getIdentifier(),
                    'title'       => $module->getTitle(),
                    'description' => $module->getDescription(),
                    'core'        => true,
                    'installed'   => true,
                ];
            }
        }
        // Apresentação
        return new ArrayIterator($result);
    }
}
