<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Wall\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Json\Decoder;
use Zend\Session\Container;
use Wall\Entity\User;
use Wall\Forms\TextStatusForm;
use Wall\Entity\Status;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        
        $session = new Container('base');
        if (!$session->offsetExists('user')) {
            $username = $this->params()->fromRoute('username');
            $client = new Client(sprintf('http://zf2-api/api/wall/%s', $username));
            $client->setMethod(\Zend\Http\Request::METHOD_GET);
            $response = $client->send();
            
            if ($response->isSuccess()) {
                $response = Decoder::decode($response->getContent(), \Zend\Json\Json::TYPE_ARRAY);
                $hydrator = new ClassMethods();
                
                $user = $hydrator->hydrate($response, new User());
            } else {
                $this->getResponse()->setStatusCode(404);
                return;
            }
            
            $session->offsetSet('user', $user);
            $session->setExpirationSeconds('user', 180);
        } else {
            $user = $session->offsetGet('user');
        }
        
        //Check if we are submitting content
        $request = $this->getRequest();
        $form = new TextStatusForm;
        $form->setInputFilter(Status::getInputFilter());
        
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $data = $form->getData();
                $data['user_id'] = $user->getId();
                
                $client = new Client(sprintf('http://zf2-api/api/wall/%s', $user->getUsername()));
                $client->setEncType(Client::ENC_URLENCODED);
                $client->setMethod(\Zend\Http\Request::METHOD_POST);
                $client->setParameterPost($data);
                $response = $client->send();
                
                if ($response->isSuccess()) {
                    if ($session->offsetExists('user')) {
                        $session->offsetUnset('user');
                    }
                    
                    return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                } else {
                    $this->getResponse()->setStatusCode(500);
                    return;
                }
            }
        }
        
        $form->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $form;
        
        return $viewData;
    }
}