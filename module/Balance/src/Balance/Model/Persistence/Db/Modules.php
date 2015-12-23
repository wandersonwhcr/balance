<?php

namespace Balance\Model\Persistence\Db;

use ArrayIterator;
use Balance\Model\BooleanType;
use Balance\Model\ModelException;
use Balance\Module\ModuleInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Camada de Persistência para Módulos
 */
class Modules implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Apresentação de Elementos
     *
     * Captura informações do projeto, verificando quais os módulos que estão disponíveis e apresentando informações
     * sobre o mesmo. Módulos instalados e não instalados podem ser filtrados.
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
                // Verificar Habilitado
                $enabled = $this->isEnabled($module);
                $capture = true;
                // Filtro de Habilitado?
                if ($params['enabled']) {
                    // Capturar Elemento?
                    $capture =
                        BooleanType::YES === $params['enabled'] && $enabled
                        || BooleanType::NO === $params['enabled'] && ! $enabled;
                }
                // Capturar?
                if ($capture) {
                    // Capturar Informações
                    $result[] = [
                        'identifier'  => $module->getIdentifier(),
                        'name'        => $module->getName(),
                        'description' => $module->getDescription(),
                        'enabled'     => $enabled,
                    ];
                }
            }
        }
        // Apresentação
        return new ArrayIterator($result);
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
        // Camada de Persistência
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(['m' => 'modules'])
            ->columns(['count' => new Expression('COUNT(1)')])
            ->where(function ($where) use ($module) {
                $where->equalTo('identifier', $module->getIdentifier());
            });
        // Consulta
        return (bool) $db->query($select->getSqlString($db->getPlatform()))->execute()->current()['count'];
    }

    /**
     * Salvar Módulos Habilitados
     *
     * @param  Parameters $data Dados para Salvamento
     * @return Modules    Próprio Objeto para Encadeamento
     */
    public function save(Parameters $data)
    {
        // Encadeamento
        return $this;
    }
}
