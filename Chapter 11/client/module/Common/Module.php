<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Common;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $e->getApplication()->getEventManager()->attach('route', array($this, 'checkAcl'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * Check acl permissions for current request
     *
     * @param MvcEvent $e 
     * @return void
     */
    public function checkAcl(MvcEvent $e) {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        $routerParams = $e->getRouteMatch()->getParams();
        $auth = new AuthenticationService();
        
        $userRole = 'guest';
        if ($auth->hasIdentity()) {
            $userRole = 'member';
            $loggedInUser = $auth->getIdentity();
            $e->getViewModel()->loggedInUser = $loggedInUser;
        }
        
        $e->getViewModel()->userRole = $userRole;
        
        if (substr($route, 0, 5) == 'feeds' && $loggedInUser->getUsername() != $routerParams['username']) {
            $response = $e->getResponse();
            $response->setStatusCode(404);
            return;
        }
        
        if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            $response = $e->getResponse();
            $response->setStatusCode(404);
            return;
        }
    }
}
