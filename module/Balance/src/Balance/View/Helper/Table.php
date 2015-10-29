<?php

namespace Balance\View\Helper;

use Balance\View\Table as TableComponent;
use Zend\View\Helper\AbstractHelper;

/**
 * Renderização de Tabela de Elementos
 */
class Table extends AbstractHelper
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        // Inicialização
        return new TableComponent();
    }
}
