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

    /**
     * Salvar Informações
     *
     * Recebe os módulos que devem ser considerados como habilitados no sistema. Estes módulos devem ser executados
     * durante a execução do Balance. Não esquecer que todos os módulos estão instalados, porém nem todos os módulos
     * estão habilitados, necessitando da configuração do usuário.
     *
     * @param  Parameters $data Dados para Salvamento
     * @return Modules    Próprio Objeto para Encadeamento
     */
    public function save(Parameters $data)
    {
        // Camada de Persistência
        $persistence = $this->getServiceLocator()->get('Balance\Model\Persistence\Modules');

        // Módulos Informados?
        if (! (array_key_exists('modules', $data) && is_array($data['modules']))) {
            // Configurar um Conjunto Vazio
            $data['modules'] = [];
        }

        // Capturar Módulos
        $dataset = $persistence->fetch(new Parameters());
        $modules = [];
        foreach ($dataset as $element) {
            $modules[] = $element['identifier'];
        }

        // Processar Módulos Informados
        foreach ($data['modules'] as $module) {
            // Existente?
            if (! in_array($module, $modules, true)) {
                // Impossível Continuar!
                throw new ModelException('Invalid Module');
            }
        }

        // Salvar Informações
        $persistence->save($data);

        // Encadeamento
        return $this;
    }
}
