<?php

namespace Balance\Model\Persistence\File;

use ArrayIterator;
use Balance\Model\BooleanType;
use Balance\Model\ModelException;
use Balance\Module\ModuleInterface;
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
     * Nome do Arquivo de Configuração
     * @type string
     */
    protected $filename;

    /**
     * Configurar Nome do Arquivo de Configuração
     *
     * @param  string  $filename Valor para Configuração
     * @return Modules Próprio Objeto para Encadeamento
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Apresentar Nome do Arquivo de Configuração
     *
     * @return string Valor Configurado
     */
    public function getFilename()
    {
        // Configurado?
        if ($this->filename === null) {
            // Configurar Valor Padrão
            $this->setFilename('./config/autoload/balance_modules.local.php');
        }
        // Apresentação
        return $this->filename;
    }

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
        // Capturar Configurações
        $modules = $this->getServiceLocator()->get('Config')['balance_modules'];
        // Apresentação
        return in_array($module->getIdentifier(), $modules, true);
    }

    /**
     * Salvar Dados de Módulos
     *
     * @param  Parameters $data Dados para Salvamento
     * @return Modules    Próprio Objeto para Encadeamento
     */
    public function save(Parameters $data)
    {
        // Podemos Gravar?
        if (! is_writable($this->getFilename())) {
            // Problema Encontrado
            throw new ModelException('File is not Writable');
        }
        // Conteúdo do Arquivo
        $content = '<?php return ' . var_export($data['modules'], true) . ';';
        // Salvar Informações
        $result = @file_put_contents($this->getFilename(), $content);
        // Sucesso?
        if (! $result) {
            // Problema Encontrado
            throw new ModelException('Internal Error');
        }
        // Encadeamento
        return $this;
    }
}
