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
            'wall' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/api/wall[/:id]',
                    'constraints' => array(
                        'id' => '\w+'
                    ),
                    'defaults' => array(
                        'controller' => 'Wall\Controller\Index'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Wall\Controller\Index' => 'Wall\Controller\IndexController',
        ),
    ),
    'akismet' => array(
        'apiKey' => '010b675653dd',
        'url' => 'http://zf2-test-book.com'
    )
);