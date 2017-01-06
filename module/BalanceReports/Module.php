<?php

namespace BalanceReports;

use Balance\Module\ModuleInterface;

/**
 * Módulo de Relatórios
 *
 * Possibilita a criação de relatórios sobre os lançamentos cadastrados.
 */
class Module implements ModuleInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'BalanceReports';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Relatórios';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return <<<DESCRIPTION
Inclui relatórios que podem ser utilizados para análise dos lançamentos efetuados no sistema, possibilitando que o
usuário visualize seus valores em formatos diversos.
DESCRIPTION;
    }
}
