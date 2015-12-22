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
                    'identifier'  => get_class($module),
                    'name'        => $module->getName(),
                    'description' => $module->getDescription(),
                    'core'        => $this->isCore($module),
                    'enabled'     => $this->isEnabled($module),
                ];
            }
        }
        // Apresentação
        return new ArrayIterator($result);
    }

    /**
     * Módulo do Sistema?
     *
     * Verifica se o módulo apresentado pertence ao conjunto de módulos que fazem parte da raiz do projeto. Sem módulos
     * de núcleo, o Balance possivelmente não existiria.
     *
     * @return bool Confirmação Solicitada
     */
    public function isCore(ModuleInterface $module)
    {
        return true;
    }

    /**
     * Módulo Habilitado?
     *
     * Verifica se o módulo apresentado está habilitado para execução no sistema. Captura as configurações do projeto e
     * apresenta uma confirmação de que o módulo solicitado foi habilitado pelo usuário no sistema.
     *
     * @return bool Confirmação Solicitada
     */
    public function isEnabled(ModuleInterface $module)
    {
        return true;
    }
}
