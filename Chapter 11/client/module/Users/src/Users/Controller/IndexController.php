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
use Users\Forms\LoginForm;
use Users\Entity\User;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;
use Zend\Authentication\AuthenticationService;
use Common\Authentication\Adapter\Api as AuthAdapter;

class IndexController extends AbstractActionController
{
    /**
     * Signup if not logged in
     *
     * @return void
     */
    public function indexAction()
    {
        $auth = new AuthenticationService();
        $loggedInUser = $auth->getIdentity();
        
        if ($loggedInUser !== null) {
            return $this->redirect()->toRoute('wall', array('username' => $loggedInUser->getUsername()));
        }
        
        $viewData = array();
        $signupForm = new SignupForm();
        $signupForm->setAttribute('action', $this->url()->fromRoute('users-signup'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $signupForm->setInputFilter(User::getInputFilter());
            $signupForm->setData($data);
            
            if ($signupForm->isValid()) {
                $files = $request->getFiles()->toArray();
                $data = $signupForm->getData();
                $data['avatar'] = $files['avatar']['name'] != '' ? $files['avatar']['name'] : null;
                
                if ($data['avatar'] !== null) {
                    $size = new Size(array('max' => 2048000));
                    $isImage = new IsImage();
                    $filename = $data['avatar'];
                    
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
                    }
                }
                
                unset($data['repeat_password']);
                unset($data['csrf']);
                unset($data['register']);
                
                $response = ApiClient::registerUser($data);
                
                if ($response['result'] == true) {
                    $auth = new AuthenticationService();
                    $authAdapter = new AuthAdapter($data['username'], $data['password']);
                    $auth->authenticate($authAdapter);
                    
                    $this->flashMessenger()->addMessage('Account created!');
                    return $this->redirect()->toRoute('wall', array('username' => $data['username']));
                }
            }
        }
        
        $viewData['signupForm'] = $signupForm;
        return $viewData;
    }
    
    /**
     * Method to login the user on the application
     *
     * @return void
     */
    public function loginAction()
    {
        $viewData = array();
        $flashMessenger = $this->flashMessenger();
        
        $loginForm = new LoginForm();
        $loginForm->setAttribute('action', $this->url()->fromRoute('users-login'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            
            $loginForm->setInputFilter(User::getLoginInputFilter());
            $loginForm->setData($data);
            
            if ($loginForm->isValid()) {
                $data = $loginForm->getData();
                
                $auth = new AuthenticationService();
                $authAdapter = new AuthAdapter($data['username'], $data['password']);
                $result = $auth->authenticate($authAdapter);
                
                if (!$result->isValid()) {
                    foreach ($result->getMessages() as $msg) {
                        $flashMessenger->addErrorMessage($msg);
                    }
                } else {
                    return $this->redirect()->toRoute('wall', array('username' => $data['username']));
                }
            }
        }
        
        $viewData['loginForm'] = $loginForm;
        
        return $viewData;
    }
    
    /**
     * Method to logout the user
     *
     * @return void
     */
    public function logoutAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }
        
        return $this->redirect()->toRoute('users-login');
    }
}