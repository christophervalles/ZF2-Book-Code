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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        
        $username = $this->params()->fromRoute('username');
        $client = new Client(sprintf('http://zf2-api/api/wall/%s', $username));
        $client->setMethod(\Zend\Http\Request::METHOD_GET);
        $response = $client->send();
        // die(var_dump($response));
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
            $data = $request->getPost();
            
            if (array_key_exists('status', $data)) {
                $statusForm = $this->createStatus($statusForm, $user, $data);
            }
            
            if (!empty($request->getFiles()->image)) {
                $data = array_merge(
                    $data->toArray(),
                    array('image' => $request->getFiles()->image)
                );
                $imageForm = $this->createImage($imageForm, $user, $data);
            }
        }
        
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $imageForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $statusForm;
        $viewData['imageContentForm'] = $imageForm;
        
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
        $form->setData($data);
        if ($form->isValid()) {
            $size = new Size(array('max' => 2048000));
            $filename = $data['image']['name'];
            
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->setValidators(array($size), $filename);
            
            if (!$adapter->isValid()){
                $dataError = $adapter->getMessages();
                $error = array();
                foreach($dataError as $key => $row) {
                    $error[] = $row;
                }
                $form->setMessages(array('image' => $error));
            } else {
                $destPath = 'data/tmp/';
                $adapter->setDestination($destPath);
                if ($adapter->receive($filename)) {
                    $data = array();
                    $data['image'] = base64_encode(
                        file_get_contents(
                            $destPath . $filename
                        )
                    );
                    $data['user_id'] = $user->getId();
                    
                    $client = new Client(sprintf('http://zf2-api/api/wall/%s', $user->getUsername()));
                    $client->setEncType(Client::ENC_URLENCODED);
                    $client->setMethod(\Zend\Http\Request::METHOD_POST);
                    $client->setParameterPost($data);
                    $response = $client->send();
                    
                    if ($response->isSuccess()) {
                        return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                }
            }
        } else {
            return $form;
        }
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
                
            if ($response->isSuccess()) {
                return $this->redirect()->toRoute('wall', array('username' => $user->getUsername()));
            } else {
                $this->getResponse()->setStatusCode(500);
                return;
            }
        } else {
            return $form;
        }
    }
}