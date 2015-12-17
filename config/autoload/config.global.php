<?php
// Definição do Timezone no PHP
date_default_timezone_set('America/Sao_Paulo');
// Definição de Locale Padrão
locale_set_default('pt_BR');
// Apresentação
return array(
    'db' => array(
        'driver'   => 'pgsql',
        'database' => 'balance',
        'username' => 'balance',
        'password' => 'balance',
        'hostname' => 'localhost',
        'port'     => '5432',
        'charset'  => 'UTF8',
    ),
);
