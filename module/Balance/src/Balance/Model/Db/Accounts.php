<?php

namespace Balance\Model\Db;

use Balance\Model\PersistenceInterface;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;

/**
 * Persistência de Dados para Contas
 */
class Accounts implements PersistenceInterface, ServiceLocatorAwareInterface
{
    /**
     * Localizador de Serviços
     * @type ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * {@inheritdoc}
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        // Resultado Inicial
        $result = array();
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'name', 'type'));
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
        // Inicialização
        $element = null;
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'name', 'type', 'description'))
            ->where(function ($where) use ($params) {
                $where->equalTo('a.id', $params['id']);
            });
        // Consulta
        $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
        // Encontrado?
        if ($row) {
            // Configuração
            $element = $row;
        }
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
        return $this;
    }
}
