<?php

namespace Common\Listeners;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use OAuth2\Storage\Pdo;
use OAuth2\Server;
use OAuth2\Request;
use OAuth2\Response;

class OAuthListener extends AbstractListenerAggregate
{
    /**
     * Holds the attached listeners
     * 
     * @var array
     */
    protected $listeners = array();
    
    /**
     * Method to register this listener on the render event
     *
     * @param EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, __CLASS__ . '::onDispatch', 1000);
    }
    
    /**
     * Method executed when the dispatch event is triggered
     *
     * @param MvcEvent $e 
     * @return void
     */
    public static function onDispatch(MvcEvent $e)
    {
        if ($e->getRequest() instanceOf \Zend\Console\Request) {
            return;
        }
        
        if ($e->getRouteMatch()->getMatchedRouteName() == 'login' || $e->getRouteMatch()->getMatchedRouteName() == 'users') {
            return;
        }
        
        $sm = $e->getApplication()->getServiceManager();
        $usersTable = $sm->get('Users\Model\UsersTable');
        
        $storage = new Pdo($usersTable->adapter->getDriver()->getConnection()->getConnectionParameters());
        $server = new Server($storage);
        if (!$server->verifyResourceRequest(Request::createFromGlobals())) {
            $model = new JsonModel(array(
                'errorCode' => $server->getResponse()->getStatusCode(),
                'errorMsg' => $server->getResponse()->getStatusText()
            ));
            
            $response = $e->getResponse();
            $response->setContent($model->serialize());
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setStatusCode($server->getResponse()->getStatusCode());
            
            return $response;
        }
    }
}
