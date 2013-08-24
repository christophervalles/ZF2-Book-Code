<?php

namespace Common\Listeners;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use OAuth2\Storage\Pdo;
use OAuth2\Server;
use OAuth2\Request;
use OAuth2\Response;

class OAuthListener implements ListenerAggregateInterface
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
     * Method to unregister the listeners
     *
     * @param EventManagerInterface $events 
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $i => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$i]);
            }
        }
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
        
        $url = $e->getRequest()->getUri()->getPath();
        
        if ($url == '/oauth' || substr($url, 0, 10) == '/api/users') {
            return;
        }
        
        $sm = $e->getApplication()->getServiceManager();
        $usersTable = $sm->get('Users\Model\UsersTable');
        
        $storage = new Pdo($usersTable->adapter->getDriver()->getConnection()->getConnectionParameters());
        $server = new Server($storage);
        if (!$server->verifyResourceRequest(Request::createFromGlobals(), new Response())) {
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
