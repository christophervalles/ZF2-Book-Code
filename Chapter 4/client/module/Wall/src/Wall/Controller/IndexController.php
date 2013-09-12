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
use Api\Client\ApiClient as ApiClient;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $viewData = array();
        
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
        
        $viewData['profileData'] = $user;
        
        return $viewData;
    }
}