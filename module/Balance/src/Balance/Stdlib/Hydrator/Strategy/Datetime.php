<?php

namespace Balance\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Estratégia de Hidratador para Data e Hora
 */
class Datetime implements StrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }
}
