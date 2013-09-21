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
use Zend\Stdlib\Hydrator\ClassMethods;
use Users\Entity\User;
use Wall\Forms\TextStatusForm;
use Wall\Entity\Status;
use Api\Client\ApiClient as ApiClient;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        $flashMessenger = $this->flashMessenger();
        
        $username = $this->params()->fromRoute('username');
        $this->layout()->username = $username;
        
        $response = ApiClient::getWall($username);
        
        if ($response !== FALSE) {
            $hydrator = new ClassMethods();
            
            $user = $hydrator->hydrate($response, new User());
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        //Check if we are submitting content
        $request = $this->getRequest();
        $statusForm = new TextStatusForm;
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            if (array_key_exists('status', $data)) {
                $result = $this->createStatus($statusForm, $user, $data);
            }
            
            switch (true) {
                case $result instanceOf TextStatusForm:
                    $statusForm = $result;
                    break;
                default:
                    if ($result == true) {
                        $flashMessenger->addSuccessMessage('New content posted!');
                        return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                    break;
            }
        }
        
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $statusForm;
        
        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }
        
        return $viewData;
    }
    
    /**
     * Create a new status
     *
     * @param Zend\Form\Form $form 
     * @param Users\Entity\User $user 
     * @param array $data
     * @return mixed
     */
    protected function createStatus($form, $user, array $data)
    {
        $form->setInputFilter(Status::getInputFilter());
        return $this->processSimpleForm($form, $user, $data);
    }
    
    /**
     * Method to process a simple form
     * User by createStatus()
     *
     * @param Zend\Form\Form $form 
     * @param string $user 
     * @param array $data 
     * @return mixed
     */
    protected function processSimpleForm($form, $user, array $data)
    {
        $form->setData($data);
        
        if ($form->isValid()) {
            $data = $form->getData();
            $data['user_id'] = $user->getId();
            unset($data['submit']);
            unset($data['csrf']);
            
            $response = ApiClient::postWallContent($user->getUsername(), $data);
            return $response['result'];
        }
        
        return $form;
    }
}