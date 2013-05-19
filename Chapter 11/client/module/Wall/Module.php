<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Wall;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $this->initAcl($e);
        
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
     * Add the ACL of this module to the global ACL
     *
     * @param MvcEvent $e 
     * @return void
     */
    public function initAcl(MvcEvent $e)
    {
        if ($e->getViewModel()->acl == null) {
            $acl = new Acl;
        } else {
            $acl = $e->getViewModel()->acl;
        }
        
        $aclConfig = include __DIR__ . '/config/module.acl.php';
        $allResources = array();
        
        foreach ($aclConfig['roles'] as $role) {
            if (!$acl->hasRole($role)) {
                $role = new Role($role);
                $acl->addRole($role);
            } else {
                $role = $acl->getRole($role);
            }
            
            if (array_key_exists($role->getRoleId(), $aclConfig['permissions'])) {
                foreach ($aclConfig['permissions'][$role->getRoleId()] as $resource) {
                    if (!$acl->hasResource($resource)) {
                        $acl->addResource(new Resource($resource));
                    }
                    $acl->allow($role, $resource);
                }
            }
        }
        
        $e->getViewModel()->acl = $acl;
    }
    
    /**
     * Check acl permissions for current request
     *
     * @param MvcEvent $e 
     * @return void
     */
    public function checkAcl(MvcEvent $e) {
        $route = $e->getRouteMatch()->getMatchedRouteName();
        $auth = new AuthenticationService();
        
        $userRole = 'guest';
        if ($auth->hasIdentity()) {
            $userRole = 'member';
            $e->getViewModel()->loggedInUser = $auth->getIdentity();
        }
        
        $e->getViewModel()->userRole = $userRole;
        
        if (!$e->getViewModel()->acl->isAllowed($userRole, $route)) {
            $response = $e->getResponse();
            $response->setStatusCode(404);
            return;
        }
    }
    
}
