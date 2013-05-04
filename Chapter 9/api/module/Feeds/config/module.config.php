<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'feeds' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/api/feeds/:username[/:id]',
                    'constraints' => array(
                        'id' => '\d+'
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index'
                    ),
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'feeds-process' => array(
                    'options' => array(
                        'route' => 'feeds process [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Feeds\Controller\Cli',
                            'action'     => 'processFeeds'
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Feeds\Controller\Index' => 'Feeds\Controller\IndexController',
            'Feeds\Controller\Cli' => 'Feeds\Controller\CliController'
        ),
    ),
);