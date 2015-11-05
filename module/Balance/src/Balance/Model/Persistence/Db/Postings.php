<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\Persistence\PersistenceInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo de Banco de Dados para Lançamentos
 */
class Postings implements ServiceLocatorAwareInterface, PersistenceInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function find(Parameters $params)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $tbPostings = $this->getServiceLocator()->get('Balance\Db\TableGateway\Postings');
        // Conversão para Banco de Dados
        $datetime = date('Y-m-d H:i:s', strtotime($data['datetime']));
        // Chave Primária?
        if ($data['id']) {
            // Atualizar Elemento
            $tbPostings->update(array(
                'datetime'    => $datetime,
                'description' => $data['description'],
            ), function ($where) use ($data) {
                $where->equalTo('id', $data['id']);
            });
        } else {
            // Inserir Elemento
            $tbPostings->insert(array(
                'datetime'    => $datetime,
                'description' => $data['description'],
            ));
            // Chave Primária
            $data['id'] = (int) $tbPostings->getLastInsertValue();
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
