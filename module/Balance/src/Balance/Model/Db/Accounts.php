<?php

namespace Balance\Model\Db;

use Balance\Model\PersistenceInterface;
use Zend\Stdlib\Parameters;

/**
 * Persistência de Dados para Contas
 */
class Accounts implements PersistenceInterface
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
