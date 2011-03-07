<?php
/**
 * Patchwork Navigation Demo Data
 *
 * @category Application
 * @package  Demo
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */
return array
(
    array(
        'label' => 'Bad Request',
        'module' => 'default',
        'controller' => 'index',
        'action' => 'badrequest',
    ),
    
    array(
        'label' => 'Module Form Example',
        'module' => 'default',
        'controller' => 'index',
        'action' => 'form',
    ),

    array(
        'label' => 'Helpers Output',
        'module' => 'default',
        'controller' => 'index',
        'action' => 'helpers',
    ),

    array(
        'label' => 'Login',
        'module' => 'user',
        'controller' => 'auth',
        'action' => 'index',
    ),

    array(
        'label' => 'Core Module',
        'module' => 'core',
        'controller' => 'test',
        'action' => 'index',
    )
);