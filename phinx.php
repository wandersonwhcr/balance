<?php
return array(
    // Caminhos
    'paths' => array(
        'migrations' => '%%PHINX_CONFIG_DIR%%/module/Balance/migrations',
    ),
    // Ambientes
    'environments' => array(
        // Configurações
        'default_migration_table' => 'phinxlog',
        'default_database'        => 'default',
        // Ambientes
        'default' => array(
            'adapter' => 'pgsql',
            'host'    => 'localhost',
            'name'    => 'balance',
            'user'    => 'balance',
            'pass'    => 'balance',
            'port'    => '5432',
            'charset' => 'utf8',
        ),
    ),
);
