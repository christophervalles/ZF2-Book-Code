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
                    'route'    => '/:username/feeds[/:feed_id]',
                    'constraints' => array(
                        'username' => '\w+',
                        'feed_id' => '\d*',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'index'
                    ),
                ),
            ),
            'feeds-subscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username/feeds/subscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'subscribe'
                    ),
                ),
            ),
            'feeds-unsubscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username/feeds/unsubscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'Feeds\Controller\Index',
                        'action' => 'unsubscribe'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Feeds\Controller\Index' => 'Feeds\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);