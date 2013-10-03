<?php

namespace Common\Listeners;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\Http\Response;

/**
 * Listener attached to render to check the response.
 * If the response contains an error a JSON model will
 * be returned containing the error followinf the
 * api problem API
 *
 * @package Common\Listeners
 */
class ApiErrorListener extends AbstractListenerAggregate
{
    /**
     * Method to register this listener on the render event
     *
     * @param EventManagerInterface $events 
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, __CLASS__ . '::onRender', 1000);
    }
    
    /**
     * Method executed when the render event is triggered
     *
     * @param MvcEvent $e 
     * @return void
     */
    public static function onRender(MvcEvent $e)
    {
        if ($e->getRequest() instanceOf \Zend\Console\Request || $e->getResponse()->isOk() || $e->getResponse()->getStatusCode() == Response::STATUS_CODE_401) {
            return;
        }
        
        $httpCode = $e->getResponse()->getStatusCode();
        $sm = $e->getApplication()->getServiceManager();
        $viewModel = $e->getResult();
        $exception = $viewModel->getVariable('exception');
        
        $model = new JsonModel(array(
            'errorCode' => !empty($exception) ? $exception->getCode() : $httpCode,
            'errorMsg' => !empty($exception) ? $exception->getMessage() : NULL
        ));
        $model->setTerminal(true);
        
        $e->setResult($model);
        $e->setViewModel($model);
        $e->getResponse()->setStatusCode($httpCode);
    }
}
