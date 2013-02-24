<?php

namespace Common\Listeners;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

/**
 * Listener attached to render to check the response.
 * If the response contains an error a JSON model will
 * be returned containing the error followinf the
 * api problem API
 *
 * @package Common\Listeners
 */
class ApiErrorListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, 'ApiErrorListener::onRender', 1000);
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
     * Method executed when the render event is triggered
     *
     * @param MvcEvent $e 
     * @return void
     */
    public static function onRender(MvcEvent $e)
    {
        if ($e->getResponse()->isOk()) {
            return;
        }
        
        $httpCode = $e->getResponse()->getStatusCode();
        $sm = $e->getApplication()->getServiceManager();
        $viewModel = $e->getResult();
        $exception = $viewModel->getVariable('exception');
        
        $model = new JsonModel(array(
            'errorCode' => $exception->getCode() ?: $httpCode,
            'errorMsg' => $exception->getMessage()
        ));
        $model->setTerminal(true);
        
        $e->setResult($model);
        $e->setViewModel($model);
        $e->getResponse()->setStatusCode($httpCode);
    }
}
