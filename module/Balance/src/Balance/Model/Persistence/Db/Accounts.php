<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\Model\Persistence\ValueOptionsInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Exception;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
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
        $result = array();
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
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'name', 'type' => $eType));
        // Pesquisa: Tipo
        if ($params['type']) {
            $select->where(function ($where) use ($params) {
                $where->equalTo('a.type', $params['type']);
            });
        }
        // Pesquisa: Palavras-Chave
        if ($params['keywords']) {
            $select->where(function ($where) use ($params) {
                $where->nest()
                    ->expression('"a"."name" ILIKE ?', '%' . $params['keywords'] . '%')
                    ->or->expression('"a"."description" ILIKE ?', '%' . $params['keywords'] . '%')
                    ->unnest();
            });
        }
        // Ordenação
        $select->order(array('a.position'));
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[] = array(
                'id'   => (int) $row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
            );
        }
        // Apresentação
        return $result;
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
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'name', 'type', 'description', 'accumulate'))
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
        $element = array(
            'id'          => (int) $row['id'],
            'type'        => $row['type'],
            'name'        => $row['name'],
            'description' => $row['description'],
            'accumulate'  => $row['accumulate'] === 't' ? BooleanType::YES : BooleanType::NO,
        );
        // Apresentação
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        // Chave Primária?
        if ($data['id']) {
            // Atualizar Elemento
            $tbAccounts->update(array(
                'type'        => $data['type'],
                'name'        => $data['name'],
                'description' => $data['description'],
                'accumulate'  => $data['accumulate'] === BooleanType::YES ? 't' : 'f',
            ), function ($where) use ($data) {
                $where->equalTo('id', $data['id']);
            });
        } else {
            // Inicialização
            $db         = $this->getServiceLocator()->get('db');
            $connection = $db->getDriver()->getConnection();
            // Tratamento
            try {
                // Inicializar Transação
                $connection->beginTransaction();
                // Consultar Última Posição
                $select = (new Select())
                    ->from(array('a' => 'accounts'))
                    ->columns(array('position' => new Expression('MAX("a"."position") + 1')));
                // Consulta
                $position = (int) $db->query($select->getSqlString($db->getPlatform()))->execute()
                    ->current()['position'];
                // Inserir Elemento
                $tbAccounts->insert(array(
                    'type'        => $data['type'],
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'position'    => $position,
                    'accumulate'  => $data['accumulate'] === BooleanType::YES ? 't' : 'f',
                ));
                // Finalização
                $connection->commit();
            } catch (Exception $e) {
                // Erro Encontrado
                $connection->rollback();
                // Apresentar Erro
                throw new ModelException('Database Error', null, $e);
            }
            // Chave Primária
            $data['id'] = (int) $tbAccounts->getLastInsertValue();
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
            throw new ModelException('Unknwon Primary Key');
        }
        // Inicialização
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        // Remover Elemento
        $count = $tbAccounts->delete(function ($delete) use ($params) {
            $delete->where(function ($where) use ($params) {
                $where->equalTo('id', $params['id']);
            });
        });
        // Sucesso?
        if ($count !== 1) {
            throw new ModelException('Unknown Element');
        }
        // Encadeamento
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        // Resultado Inicial
        $result = array();
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'name'))
            ->order(array('name'));
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[$row['id']] = $row['name'];
        }
        // Apresentação
        return $result;
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
        $before = (int) $params['before'];
        $after  = (int) $params['after'];
        // Antes?
        if ($before < 0) {
            // Impossível Continuar
            throw new ModelException('Invalid "before" Parameter');
        }
        // Depois?
        if ($after < 0) {
            // Impossível Continuar
            throw new ModelException('Invalid "after" Parameter');
        }
        // Válidos?
        if ($after === $before) {
            // Impossível Continuar
            throw new ModelException('Invalid Parameters');
        }
        // Inicialização
        $tbAccounts = $this->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        $connection = $this->getServiceLocator()->get('db')->getDriver()->getConnection();
        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();
            // Deslocando para Frente?
            if ($before < $after) {
                // Empurrar Todos os "Posterior"
                $tbAccounts->update(array(
                    'position' => new Expression('"position" + 1'),
                ), function ($where) use ($after) {
                    $where->greaterThan('position', $after);
                });
                // Colocar o "Anterior" no "Posterior"
                $tbAccounts->update(array(
                    'position' => $after + 1,
                ), function ($where) use ($before) {
                    $where->equalTo('position', $before);
                });
                // Puxar Todos os "Anterior"
                $tbAccounts->update(array(
                    'position' => new Expression('"position" - 1'),
                ), function ($where) use ($before) {
                    $where->greaterThan('position', $before);
                });
            }
            // Deslocando para Trás?
            if ($before > $after) {
                // Empurrar Todos para "Anterior"
                $tbAccounts->update(array(
                    'position' => new Expression('"position" - 1'),
                ), function ($where) use ($after) {
                    $where->lessThan('position', $after);
                });
                // Colocar o "Anterior" no "Posterior"
                $tbAccounts->update(array(
                    'position' => $after - 1,
                ), function ($where) use ($before) {
                    $where->equalTo('position', $before);
                });
                // Puxar Todos os "Posterior"
                $tbAccounts->update(array(
                    'position' => new Expression('"position" + 1'),
                ), function ($where) use ($before) {
                    $where->lessThan('position', $before);
                });
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
}
