<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\Persistence\PersistenceInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo de Banco de Dados para Lançamentos
 */
class Postings implements PersistenceInterface
{
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
