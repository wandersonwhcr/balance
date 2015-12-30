<?php

namespace Balance\Model\Persistence\Db;

use ArrayIterator;
use ArrayObject;
use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\Model\Persistence\ValueOptionsInterface;
use Exception;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Persistência de Dados para Contas
 */
class Accounts implements PersistenceInterface, ServiceLocatorAwareInterface, ValueOptionsInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        // Resultado Inicial
        $result = [];
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Expressão: Tipo
        $expression = 'CASE "a"."type"';
        $definition = (new AccountType())->getDefinition();
        foreach ($definition as $identifier => $value) {
            $expression = $expression . sprintf(" WHEN '%s' THEN '%s'", $identifier, $value);
        }
        $expression = $expression . ' END';
        // Construtor
        $eType = new Expression($expression);
        // Seletor
        $select = (new Select())
            ->from(['a' => 'accounts'])
            ->columns(['id', 'name', 'type' => $eType]);
        // Pesquisa: Tipo
        if ($params['type']) {
            $select->where(function ($where) use ($params) {
                $where->equalTo('a.type', $params['type']);
            });
        }
        // Pesquisa: Palavras-Chave
        if ($params['keywords']) {
            // Filtro
            $select->where(function ($where) use ($params) {
                // Idioma
                $language = locale_get_display_language(null, 'en');
                // Documento
                $document = new Expression(
                    'TO_TSVECTOR(\'' . $language . '\', "a"."name")'
                    . ' || TO_TSVECTOR(\'' . $language . '\', "a"."description")'
                );
                // Construção do Documento
                $search = (new Select())
                    ->from(['a' => 'accounts'])
                    ->columns(['account_id' => 'id', 'document' => $document]);
                // Pesquisa Interna
                $subselect = (new Select())
                    ->from(['search' => $search])
                    ->columns(['account_id'])
                    ->where(function ($where) use ($params, $language) {
                        $where->expression(
                            '"search"."document" @@ TO_TSQUERY(\'' . $language . '\', ?)',
                            sprintf("'%s'", addslashes($params['keywords']))
                        );
                    });
                // Aplicação do Filtro
                $where->in('a.id', $subselect);
            });
        }
        // Ordenação
        $select->order(['a.position']);
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[] = [
                'id'   => (int) $row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
            ];
        }
        // Apresentação
        return new ArrayIterator($result);
    }

    /**
     * {@inheritdoc}
     */
    public function find(Parameters $params)
    {
        // Chave Primária?
        if (! $params['id']) {
            throw new ModelException('Unknown Primary Key');
        }
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(['a' => 'accounts'])
            ->columns(['id', 'name', 'type', 'description', 'accumulate'])
            ->where(function ($where) use ($params) {
                $where->equalTo('a.id', (int) $params['id']);
            });
        // Consulta
        $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
        // Encontrado?
        if (! $row) {
            throw new ModelException('Unknown Element');
        }
        // Configurações
        $element = [
            'id'          => (int) $row['id'],
            'type'        => $row['type'],
            'name'        => $row['name'],
            'description' => $row['description'],
            'accumulate'  => $row['accumulate'] === 't' ? BooleanType::YES : BooleanType::NO,
        ];
        // Apresentação
        return new ArrayObject($element);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        $db         = $this->getServiceLocator()->get('db');
        $connection = $db->getDriver()->getConnection();
        // Tratamento
        try {
            // Inicializar Transação
            $connection->beginTransaction();
            // Chave Primária?
            if ($data['id']) {
                // Atualizar Elemento
                $tbAccounts->update([
                    'type'        => $data['type'],
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'accumulate'  => $data['accumulate'] === BooleanType::YES ? 't' : 'f',
                ], function ($where) use ($data) {
                    $where->equalTo('id', $data['id']);
                });
            } else {
                // Consultar Última Posição
                $select = (new Select())
                    ->from(['a' => 'accounts'])
                    ->columns(['position' => new Expression('MAX("a"."position") + 1')]);
                // Consulta
                $position = (int) $db->query($select->getSqlString($db->getPlatform()))->execute()
                    ->current()['position'];
                // Inserir Elemento
                $tbAccounts->insert([
                    'type'        => $data['type'],
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'position'    => $position,
                    'accumulate'  => $data['accumulate'] === BooleanType::YES ? 't' : 'f',
                ]);
                // Chave Primária
                $data['id'] = (int) $tbAccounts->getLastInsertValue();
            }
            // Finalização
            $connection->commit();
        } catch (Exception $e) {
            // Erro Encontrado
            $connection->rollback();
            // Apresentar Erro
            throw new ModelException('Database Error', null, $e);
        }
        // Encadeamento
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Parameters $params)
    {
        // Chave Primária?
        if (! $params['id']) {
            throw new ModelException('Unknown Primary Key');
        }
        // Inicialização
        $db         = $this->getServiceLocator()->get('db');
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        $connection = $db->getDriver()->getConnection();
        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();
            // Seletor
            $select = (new Select())
                ->from(['a' => 'accounts'])
                ->columns(['position'])
                ->where(function ($where) use ($params) {
                    $where->equalTo('a.id', $params['id']);
                });
            // Consultar Posição
            $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
            // Encontrado?
            if (! $row) {
                throw new ModelException('Unknown Element');
            }
            // Remover Elemento
            $tbAccounts->delete(function ($delete) use ($params) {
                $delete->where(function ($where) use ($params) {
                    $where->equalTo('id', $params['id']);
                });
            });
            // Reordenar Contas
            $tbAccounts->update([
                'position' => new Expression('"position" - 1'),
            ], function ($where) use ($row) {
                $where->greaterThan('position', $row['position']);
            });
            // Confirmação
            $connection->commit();
        } catch (Exception $e) {
            // Retorno
            $connection->rollback();
            // Apresentar Erro
            throw new ModelException('Database Error', null, $e);
        }
        // Encadeamento
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        // Definições de Nomeclatura
        $definition = (new AccountType())->getDefinition();
        // Resultado Inicial
        $result = [
            AccountType::ACTIVE  => [
                'label'   => $definition[AccountType::ACTIVE],
                'options' => [],
            ],
            AccountType::PASSIVE => [
                'label'   => $definition[AccountType::PASSIVE],
                'options' => [],
            ],
        ];
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(['a' => 'accounts'])
            ->columns(['id', 'type', 'name'])
            ->order(['a.type', 'a.name']);
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[$row['type']]['options'][$row['id']] = $row['name'];
        }
        // Remover Conjuntos Vazios
        foreach ($result as $identifier => $container) {
            if (! $container['options']) {
                unset($result[$identifier]);
            }
        }
        // Limpeza de Chaves no Primeiro Nível
        $result = array_values($result);
        // Apresentação
        return $result;
    }

    /**
     * Captura de Posição
     *
     * @param  int            $id Chave Primária
     * @throws ModelException Elemento não Encontrado
     * @return int            Posição Encontrada
     */
    private function getPosition($id)
    {
        // Inicialização
        $db = $this->getServiceLocator()->get('db');
        // Capturador de Posições
        $select = (new Select())
            ->from(['a' => 'accounts'])
            ->columns(['position'])
            ->where(function ($where) use ($id) {
                $where->equalTo('a.id', $id);
            });
        // Consulta
        $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
        // Encontrado?
        if (! $row) {
            throw new ModelException('Unknown Element');
        }
        // Apresentar Posição
        return (int) $row['position'];
    }

    /**
     * Ordenar Elementos
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return Accounts   Próprio Objeto para Encadeamento
     */
    public function order(Parameters $params)
    {
        // Inicialização
        $id       = (int) $params['id'];
        $previous = (int) $params['previous'];

        // Inicialização
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        $db         = $this->getServiceLocator()->get('db');
        $connection = $db->getDriver()->getConnection();

        // Capturar Posição do Elemento
        $positionBefore = $this->getPosition($id);
        $positionAfter  = -1;

        // Elemento Anterior Enviado?
        if ($previous) {
            // Capturar Elemento
            $positionAfter = $this->getPosition($previous);
        }

        // Mesma Posição?
        if ($positionBefore === $positionAfter) {
            // Encadeamento
            return $this;
        }

        // Posição Anterior Maior que Posição Posterior?
        if ($positionBefore > $positionAfter) {
            // Posição Posterior é de Elemento que não Participa do Intervalo que Modifica Posição
            $positionAfter = $positionAfter + 1;
        }

        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();

            // Parâmetros (Antes === Depois) Não Existe Aqui!
            $parameters = [$positionBefore, $positionAfter, ($positionBefore < $positionAfter ? '-1' : '+1')];
            // Expressão
            $expression = new Expression('(CASE WHEN "position" = ? THEN ? ELSE "position" + ? END)', $parameters);

            // Atualização para Frente
            $tbAccounts->update([
                'position' => $expression,
            ], function ($where) use ($positionBefore, $positionAfter) {
                $where->between(
                    'position',
                    min($positionBefore, $positionAfter),
                    max($positionBefore, $positionAfter)
                );
            });

            // Finalização
            $connection->commit();
        } catch (Exception $e) {
            // Erro Encontrado
            $connection->rollback();
            // Apresentar Erro
            throw new ModelException('Database Error', null, $e);
        }

        // Encadeamento
        return $this;
    }
}
