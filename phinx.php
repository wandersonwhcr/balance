<?php
// Configuração Básica
$config = include './config/application.config.php';
// Configurações Adicionais
foreach ($config['module_listener_options']['config_glob_paths'] as $pattern) {
    // Captura de Arquivos
    foreach (glob($pattern, GLOB_BRACE) as $filename) {
        // Carregamento
        $config = call_user_func(['Zend\Stdlib\ArrayUtils', 'merge'], $config, include $filename);
    }
}
// Apresentação
return [
    // Caminhos
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/module/Balance/migrations',
    ],
    // Ambientes
    'environments' => [
        // Configurações
        'default_migration_table' => 'phinxlog',
        'default_database'        => 'default',
        // Ambientes
        'default' => [
            'adapter' => $config['db']['driver'],
            'host'    => $config['db']['hostname'],
            'name'    => $config['db']['database'],
            'user'    => $config['db']['username'],
            'pass'    => $config['db']['password'],
            'port'    => $config['db']['port'],
            'charset' => $config['db']['charset'],
        ],
    ],
];
