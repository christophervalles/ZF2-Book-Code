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
            'news' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username/news[/:feed_id]',
                    'constraints' => array(
                        'username' => '\w+',
                        'feed_id' => '\d*',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\Index',
                        'action' => 'index'
                    ),
                ),
            ),
            'news-subscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username/news/subscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\Index',
                        'action' => 'subscribe'
                    ),
                ),
            ),
            'news-unsubscribe' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:username/news/unsubscribe',
                    'constraints' => array(
                        'username' => '\w+',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\Index',
                        'action' => 'unsubscribe'
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'News\Controller\Index' => 'News\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);