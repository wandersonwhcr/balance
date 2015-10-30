<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Persistência de Dados para Contas
 */
class Accounts implements PersistenceInterface, ServiceLocatorAwareInterface
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
            ->columns(array('id', 'name', 'type', 'description'))
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
            ), function ($where) use ($data) {
                $where->equalTo('id', $data['id']);
            });
        } else {
            // Inserir Elemento
            $tbAccounts->insert(array(
                'type'        => $data['type'],
                'name'        => $data['name'],
                'description' => $data['description'],
            ));
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
}
