<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Balance\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
            'accounts' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/accounts',
                    'defaults' => array(
                        'controller' => 'Balance\Controller\Accounts',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Balance\Controller\Home'     => 'Balance\Controller\Home',
            'Balance\Controller\Accounts' => 'Balance\Controller\Accounts',
        ),
    ),

    'view_manager' => array(
        'doctype' => 'HTML5',

        'display_exceptions'       => false,
        'display_not_found_reason' => false,

        'not_found_template' => 'error/404',
        'exception_template' => 'error/500',

        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/500'     => __DIR__ . '/../view/error/500.phtml',
        ),

        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
