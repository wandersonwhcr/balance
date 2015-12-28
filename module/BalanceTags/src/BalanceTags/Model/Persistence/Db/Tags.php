<?php

namespace BalanceTags\Model\Persistence\Db;

use ArrayIterator;
use Balance\Model\Persistence\PersistenceInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Persistência de Etiquetas
 */
class Tags implements PersistenceInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        return new ArrayIterator(array());
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
