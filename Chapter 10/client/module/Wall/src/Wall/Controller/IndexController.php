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
use Wall\Forms\ImageForm;
use Wall\Forms\LinkForm;
use Wall\Forms\CommentForm;
use Wall\Entity\Status;
use Wall\Entity\Wall;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;
use Api\Client\ApiClient;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        $flashMessenger = $this->flashMessenger();
        
        $username = $this->params()->fromRoute('username');
        $this->layout()->username = $username;
        
        $userData = ApiClient::getUser($username);
        
        if ($userData !== FALSE) {
            $hydrator = new ClassMethods();
            
            $user = $hydrator->hydrate($userData, new User());
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $hydrator = new ClassMethods();
        $wallData = ApiClient::getWall($username);
        $wall = $hydrator->hydrate($wallData, new Wall());
        
        $paginator = new Paginator(new ArrayAdapter($wall->getFeed()));
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        
        //Check if we are submitting content
        $request = $this->getRequest();
        $statusForm = new TextStatusForm;
        $imageForm = new ImageForm();
        $linkForm = new LinkForm();
        $commentForm = new CommentForm();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            if (array_key_exists('status', $data)) {
                $result = $this->createStatus($statusForm, $user, $data);
            }
            
            if (!empty($request->getFiles()->image)) {
                $data = array_merge_recursive(
                    $data,
                    $request->getFiles()->toArray()
                );
                $result = $this->createImage($imageForm, $user, $data);
            }
            
            if (array_key_exists('url', $data)) {
                $result = $this->createLink($linkForm, $user, $data);
            }
            
            if (array_key_exists('comment', $data)) {
                $result = $this->createComment($commentForm, $user, $data);
            }
            
            switch (true) {
                case $result instanceOf TextStatusForm:
                    $statusForm = $result;
                    break;
                case $result instanceOf ImageForm:
                    $imageForm = $result;
                    break;
                case $result instanceOf LinkForm:
                    $linkForm = $result;
                    break;
                case $result instanceOf CommentForm:
                    $commentForm = $result;
                    break;
                default:
                    if ($result == true) {
                        $flashMessenger->addMessage('New content posted!');
                        return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                    break;
            }
        }
        
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $imageForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $linkForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $commentForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $viewData['user'] = $user;
        $viewData['textContentForm'] = $statusForm;
        $viewData['imageContentForm'] = $imageForm;
        $viewData['linkContentForm'] = $linkForm;
        $viewData['commentContentForm'] = $commentForm;
        $viewData['isMyWall'] = !empty($loggedInUser)? $loggedInUser->getUsername() == $username : false;
        $viewData['username'] = $username;
        $viewData['paginator'] = $paginator;
        
        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }
        
        return $viewData;
    }
    
    /**
     * Upload a new image
     *
     * @param Zend\Form\Form $form 
     * @param Users\Entity\User $user 
     * @param array $data
     */
    protected function createImage($form, $user, $data)
    {
        if ($data['image']['error'] != 0) {
            $data['image'] = NULL;
        }
        
        $form->setData($data);
        
        $size = new Size(array('max' => 2048000));
        $isImage = new IsImage();
        $filename = $data['image']['name'];
        
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        $adapter->setValidators(array($size, $isImage), $filename);
        
        if (!$adapter->isValid($filename)){
            $errors = array();
            foreach($adapter->getMessages() as $key => $row) {
                $errors[] = $row;
            }
            $form->setMessages(array('image' => $errors));
        }
        
        if ($form->isValid()) {
            $destPath = 'data/tmp/';
            $adapter->setDestination($destPath);
            
            $fileinfo = $adapter->getFileInfo();
            preg_match('/.+\/(.+)/', $fileinfo['image']['type'], $matches);
            $extension = $matches[1];
            $newFilename = sprintf('%s.%s', sha1(uniqid(time(), true)), $extension);
            
            $adapter->addFilter('File\Rename',
                array(
                    'target' => $destPath . $newFilename,
                    'overwrite' => true,
                )
            );
            
            if ($adapter->receive($filename)) {
                $data = array();
                $data['image'] = base64_encode(
                    file_get_contents(
                        $destPath . $newFilename
                    )
                );
                $data['user_id'] = $user->getId();
                
                if (file_exists($destPath . $newFilename)) {
                    unlink($destPath . $newFilename);
                }
                
                $response = ApiClient::postWallContent($user->getUsername(), $data);
                return $response['result'];
            }
        }
        
        return $form;
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
     * Store a new link
     *
     * @param Zend\Form\Form $form 
     * @param Users\Entity\User $user 
     * @param array $data
     * @return mixed
     */
    protected function createLink($form, $user, array $data)
    {
        return $this->processSimpleForm($form, $user, $data);
    }
    
    /**
     * Store a new comment
     *
     * @param Zend\Form\Form $form 
     * @param Users\Entity\User $user 
     * @param array $data
     * @return mixed
     */
    protected function createComment($form, $user, array $data)
    {
        return $this->processSimpleForm($form, $user, $data);
    }
    
    /**
     * Method to process a simple form
     * User by createStatus() and createLink()
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