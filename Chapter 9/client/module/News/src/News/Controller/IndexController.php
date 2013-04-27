<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Client\ApiClient as ApiClient;
use News\Form\SubscribeForm as SubscribeForm;
use News\Form\UnsubscribeForm as UnsubscribeForm;

class IndexController extends AbstractActionController
{
    /**
     * Get the feed list and the posts of the feed we are looking at now
     *
     * @return void
     */
    public function indexAction()
    {
        $viewData = array();
        
        $subscribeForm = new SubscribeForm();
        $unsubscribeForm = new UnsubscribeForm();
        $subscribeForm->setAttribute('action', $this->url()->fromRoute('news-subscribe', array('username' => $user->getUsername())));
        $unsubscribeForm->setAttribute('action', $this->url()->fromRoute('news-unsubscribe', array('username' => $user->getUsername())));
        
        $username = $this->params()->fromRoute('username');
        $feeds = $response = ApiClient::getFeeds($username);
        $currentFeedId = $this->params()->fromRoute('feed_id');
        
        if ($currentFeedId === null && !empty($feeds)) {
            $currentFeedId = $feeds[0]->id;
        }
        
        $articles = array();
        if ($currentFeedId !== null) {
            $articles = ApiClient::getFeedArticles($username, $currentFeedId);
        }
        
        $viewData['subscribeForm'] = $subscribeForm;
        $viewData['unsubscribeForm'] = $unsubscribeForm;
        $viewData['feeds'] = $feeds;
        $viewData['articles'] = $articles;
        
        return $viewData;
    }
    
    /**
     * Add a new subscription for the specified user
     *
     * @return void
     */
    public function susbcribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $result = ApiClient::addFeedSubscription($username, $data);
            
            if ($result === TRUE) {
                $flashMessenger->addMessage('Subscribed successfully!');
                return $this->redirect()->toRoute('news', array('username' => $user->getUsername()));
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }
    }
    
    /**
     * Unsubscribe a user from a specific feed
     *
     * @return void
     */
    public function unsusbcribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $result = ApiClient::removeFeedSubscription($username, $data);
            
            if ($result === TRUE) {
                $flashMessenger->addMessage('Unsubscribed successfully!');
                return $this->redirect()->toRoute('news', array('username' => $user->getUsername()));
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }
    }
}