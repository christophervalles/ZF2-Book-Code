<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Common;

use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * Convenience method to return the config file
     *
     * @return string
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    /**
     * Return an autoloader configured namespace
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }
    
    /**
     * Attaches the ApiErrorListener on the render event
     *
     * @param MvcEvent $e
     */
    public function onBootstrap($e)
    {
        $app = $e->getTarget();
        $sm = $app->getServiceManager();
        $events = $app->getEventManager();
        $events->attach($sm->get('Common\Listeners\ApiErrorListener'));
    }
}