<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Client\ApiClient;
use Users\Forms\SignupForm;
use Users\Entity\User;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;

class IndexController extends AbstractActionController
{
    /**
     * Get the feed list and the posts of the feed we are looking at now
     *
     * @return void
     */
    public function indexAction()
    {
        $this->layout('layout/signup');
        
        $viewData = array();
        $signupForm = new SignupForm();
        $signupForm->setAttribute('action', $this->url()->fromRoute('users-signup'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $signupForm->setInputFilter(User::getInputFilter());
            $signupForm->setData($data);
            
            if ($signupForm->isValid()) {
                $data = array_merge_recursive(
                    $signupForm->getData(),
                    $request->getFiles()->toArray()
                );
                
                $size = new Size(array('max' => 2048000));
                $isImage = new IsImage();
                $filename = $data['avatar']['name'];
                
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size, $isImage), $filename);
                
                if (!$adapter->isValid($filename)){
                    $errors = array();
                    foreach($adapter->getMessages() as $key => $row) {
                        $errors[] = $row;
                    }
                    $signupForm->setMessages(array('avatar' => $errors));
                }
                
                $destPath = 'data/tmp/';
                $adapter->setDestination($destPath);
                
                $fileinfo = $adapter->getFileInfo();
                preg_match('/.+\/(.+)/', $fileinfo['avatar']['type'], $matches);
                $extension = $matches[1];
                $newFilename = sprintf('%s.%s', sha1(uniqid(time(), true)), $extension);
                
                $adapter->addFilter('File\Rename',
                    array(
                        'target' => $destPath . $newFilename,
                        'overwrite' => true,
                    )
                );
                
                if ($adapter->receive($filename)) {
                    $data['avatar'] = base64_encode(
                        file_get_contents(
                            $destPath . $newFilename
                        )
                    );
                    
                    if (file_exists($destPath . $newFilename)) {
                        unlink($destPath . $newFilename);
                    }
                    
                    unset($data['repeat_password']);
                    unset($data['csrf']);
                    unset($data['register']);
                    
                    $response = ApiClient::registerUser($data);
                    
                    if ($response['result'] == true) {
                        $this->flashMessenger()->addMessage('Account created!');
                        return $this->redirect()->toRoute('wall', array('username' => $data['username']));
                    }
                }
            }
        }
        
        $viewData['signupForm'] = $signupForm;
        return $viewData;
    }
}