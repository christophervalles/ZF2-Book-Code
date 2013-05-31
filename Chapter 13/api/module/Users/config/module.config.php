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
            'users' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route' => '/api/users[/:id]',
                    'defaults' => array(
                        'controller' => 'Users\Controller\Index'
                    ),
                ),
            ),
            'login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/api/users/login',
                    'defaults' => array(
                        'controller' => 'Users\Controller\Login'
                    ),
                ),
            ),
        ),
    ),
    'di' => array(
        'services' => array(
            'Users\Model\UsersTable' => 'Users\Model\UsersTable',
            'Users\Model\UserStatusesTable' => 'Users\Model\UserStatusesTable',
            'Users\Model\UserLinksTable' => 'Users\Model\UserLinksTable',
            'Users\Model\UserImagesTable' => 'Users\Model\UserImagesTable',
            'Users\Model\UserCommentsTable' => 'Users\Model\UserCommentsTable',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Users\Controller\Index' => 'Users\Controller\IndexController',
            'Users\Controller\Login' => 'Users\Controller\LoginController',
        ),
    ),
);