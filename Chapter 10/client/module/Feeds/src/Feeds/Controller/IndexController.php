<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feeds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Client\ApiClient;
use Feeds\Forms\SubscribeForm;
use Feeds\Forms\UnsubscribeForm;
use Users\Entity\User;
use Zend\Stdlib\Hydrator\ClassMethods;
use Feeds\Entity\Feed;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\AbstractPage;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

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
        
        $flashMessenger = $this->flashMessenger();
        
        $username = $this->params()->fromRoute('username');
        $this->layout()->username = $username;
        
        $currentFeedId = $this->params()->fromRoute('feed_id');
        
        $userData = ApiClient::getUser($username);
        if ($userData !== FALSE) {
            $hydrator = new ClassMethods();
            
            $user = $hydrator->hydrate($userData, new User());
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $subscribeForm = new SubscribeForm();
        $unsubscribeForm = new UnsubscribeForm();
        $subscribeForm->setAttribute('action', $this->url()->fromRoute('feeds-subscribe', array('username' => $username)));
        $unsubscribeForm->setAttribute('action', $this->url()->fromRoute('feeds-unsubscribe', array('username' => $username)));
        
        $hydrator = new ClassMethods();
        $response = ApiClient::getFeeds($username);
        $feeds = array();
        foreach ($response as $r) {
            $feeds[$r['id']] = $hydrator->hydrate($r, new Feed());
        }
        
        if ($currentFeedId === null && !empty($feeds)) {
            $currentFeedId = reset($feeds)->getId();
        }
        
        $feedsMenu = new Navigation();
        $router = $this->getEvent()->getRouter();
        $routeMatch = $this->getEvent()->getRouteMatch()->setParam('feed_id', $currentFeedId);
        foreach ($feeds as $f) {
            $feedsMenu->addPage(
                AbstractPage::factory(array(
                    'title' => $f->getTitle(),
                    'icon' => $f->getIcon(),
                    'route' => 'feeds',
                    'routeMatch' => $routeMatch,
                    'router' => $router,
                    'params' => array('username' => $username, 'feed_id' => $f->getId())
                ))
            );
        }
        
        $currentFeed = $currentFeedId != null? $feeds[$currentFeedId] : null;
        
        if ($currentFeed != null) {
            $paginator = new Paginator(new ArrayAdapter($currentFeed->getArticles()));
            $paginator->setItemCountPerPage(5);
            $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
            $viewData['paginator'] = $paginator;
            $viewData['feedId'] = $currentFeedId;
        }
        
        $unsubscribeForm->get('feed_id')->setValue($currentFeedId);
        
        $viewData['subscribeForm'] = $subscribeForm;
        $viewData['unsubscribeForm'] = $unsubscribeForm;
        $viewData['username'] = $username;
        $viewData['feedsMenu'] = $feedsMenu;
        $viewData['user'] = $user;
        $viewData['feed'] = $currentFeed;
        
        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }
        
        return $viewData;
    }
    
    /**
     * Add a new subscription for the specified user
     *
     * @return void
     */
    public function subscribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $response = ApiClient::addFeedSubscription($username, array('url' => $data['url']));
            
            if ($response['result'] == TRUE) {
                $this->flashMessenger()->addMessage('Subscribed successfully!');
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }
        
        return $this->redirect()->toRoute('feeds', array('username' => $username));
    }
    
    /**
     * Unsubscribe a user from a specific feed
     *
     * @return void
     */
    public function unsubscribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $response = ApiClient::removeFeedSubscription($username, $data['feed_id']);
            
            if ($response['result'] == TRUE) {
                $this->flashMessenger()->addMessage('Unsubscribed successfully!');
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }
        
        return $this->redirect()->toRoute('feeds', array('username' => $username));
    }
}