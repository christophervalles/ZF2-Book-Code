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
use Wall\Entity\User;
use Wall\Forms\TextStatusForm;
use Wall\Forms\ImageForm;
use Wall\Entity\Status;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        $flashMessenger = $this->flashMessenger();
        
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
        
        //Check if we are submitting content
        $request = $this->getRequest();
        $statusForm = new TextStatusForm;
        $imageForm = new ImageForm();
        
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            if (array_key_exists('status', $data)) {
                $result = $this->createStatus($statusForm, $user, $data);
                
                if ($result instanceOf TextStatusForm) {
                    $statusForm = $result;
                } else {
                    if ($result === TRUE) {
                        $flashMessenger->addMessage('New status posted!');
                        return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                }
            }
            
            if (!empty($request->getFiles()->image)) {
                $data = array_merge_recursive(
                    $data,
                    $request->getFiles()->toArray()
                );
                $result = $this->createImage($imageForm, $user, $data);
                
                if ($result instanceOf ImageForm) {
                    $imageForm = $result;
                } else {
                    if ($result === TRUE) {
                        $this->flashMessenger()->addMessage('Your image has been posted!');
                        
                        return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                }
            }
        }
        
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $imageForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $statusForm;
        $viewData['imageContentForm'] = $imageForm;
        
        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }
        
        return $viewData;
    }
    
    /**
     * Upload a new image
     *
     * @param Zend\Form\Form $form 
     * @param Wall\Entity\User $user 
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
            $newFilename = sprintf('%s.%s', sha1(uniqid(time(), TRUE)), $extension);
                
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
                    
                $client = new Client(sprintf('http://zf2-api/api/wall/%s', $user->getUsername()));
                $client->setEncType(Client::ENC_URLENCODED);
                $client->setMethod(\Zend\Http\Request::METHOD_POST);
                $client->setParameterPost($data);
                $response = $client->send();
                
                if (file_exists($destPath . $newFilename)) {
                    unlink($destPath . $newFilename);
                }       
                
                return $response->isSuccess();
            }
        }
        return $form;
    }
    
    /**
     * Create a new status
     *
     * @param Zend\Form\Form $form 
     * @param Wall\Entity\User $user 
     * @param array $data
     */
    protected function createStatus($form, $user, array $data)
    {
        $form->setInputFilter(Status::getInputFilter());
        $form->setData($data);
        
        if ($form->isValid()) {
            $data = $form->getData();
            $data['user_id'] = $user->getId();
                
            $client = new Client(sprintf('http://zf2-api/api/wall/%s', $user->getUsername()));
            $client->setEncType(Client::ENC_URLENCODED);
            $client->setMethod(\Zend\Http\Request::METHOD_POST);
            $client->setParameterPost($data);
            $response = $client->send();
                
            return $response->isSuccess();
        }
        
        return $form;
    }
}