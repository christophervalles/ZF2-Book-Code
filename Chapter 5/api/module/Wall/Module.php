<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Wall;

use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * Method executed when bootstrapping the module, we use it to setup a few listeners
     * to various events like dispatch or error dispatch
     *
     * @param MvcEvent $e 
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $moduleManager = $e->getApplication()->getServiceManager()->get('modulemanager');
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach('Zend\Mvc\Controller\AbstractRestfulController', MvcEvent::EVENT_DISPATCH, array($this, 'postProcess'), -100);
        $sharedEvents->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'errorProcess'), 999);
    }
    
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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * Method executed when the request is dispatched. We use a PostProcessor to
     * transform the response to a JSON object
     *
     * @param MvcEvent $e 
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public function postProcess(MvcEvent $e)
    {
        if ($e->getResponse()->getStatusCode() == 200) {
            $di = $e->getTarget()->getServiceLocator()->get('di');
            
            if (is_array($e->getResult()->getVariables())) {
                $vars = $e->getResult()->getVariables();
            } else {
                $vars = null;
            }
            
            $postProcessor = $di->get('JsonPostProcessor', array(
                'response' => $e->getResponse(),
                'vars' => $vars,
            ));
            
            $postProcessor->process();
            
            return $postProcessor->getResponse();
        }
        
        return $e->getResponse();
    }
    
    /**
     * @param MvcEvent $e
     * @return null|\Zend\Http\PhpEnvironment\Response
     */
    public function errorProcess(MvcEvent $e)
    {
        $di = $e->getApplication()->getServiceManager()->get('di');
        $eventParams = $e->getParams();
        $config = $e->getApplication()->getConfig();
        
        $vars = array();
        if (isset($eventParams['exception'])) {
            $exception = $eventParams['exception'];
            
            if ($config['errors']['show_exceptions']['message']) {
                $vars['error-message'] = $exception->getMessage();
            }
            if ($config['errors']['show_exceptions']['trace']) {
                $vars['error-trace'] = $exception->getTrace();
            }
        }
        
        if (empty($vars)) {
            $vars['error'] = 'Something went wrong';
        }
        
        $postProcessor = $di->get(
            'JsonPostProcessor',
            array('vars' => $vars, 'response' => $e->getResponse())
        );
        
        $postProcessor->process();
        
        if (
            $eventParams['error'] === \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND ||
            $eventParams['error'] === \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH
        ) {
            $e->getResponse()->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_501);
        } else {
            $e->getResponse()->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_500);
        }
        
        $e->stopPropagation();
        
        return $postProcessor->getResponse();
    }
}