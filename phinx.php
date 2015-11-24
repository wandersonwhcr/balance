<?php
// Configuração Básica
$config = include './config/application.config.php';
// Configurações Adicionais
foreach ($config['module_listener_options']['config_glob_paths'] as $pattern) {
    // Captura de Arquivos
    foreach (glob($pattern, GLOB_BRACE) as $filename) {
        // Carregamento
        $config = array_merge($config, include $filename);
    }
}
// Apresentação
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
            'adapter' => $config['db']['driver'],
            'host'    => $config['db']['hostname'],
            'name'    => $config['db']['database'],
            'user'    => $config['db']['username'],
            'pass'    => $config['db']['password'],
            'port'    => $config['db']['port'],
            'charset' => $config['db']['charset'],
        ),
    ),
);
