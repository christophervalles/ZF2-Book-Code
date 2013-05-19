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

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $this->initAcl($e);
        
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
}
