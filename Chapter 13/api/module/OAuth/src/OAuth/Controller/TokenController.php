<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OAuth\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * This class is the responsible to answer the requests to the /oauth/token endpoint
 *
 * @package OAuth/Controller
 */
class TokenController extends AbstractRestfulController
{
    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;
    
    public function get($id)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Get a token
     *
     * @return void
     */
    public function getList()
    {
        $usersTable = $this->getUsersTable();
        
        $storage = new \OAuth2_Storage_Pdo($usersTable->adapter->getDriver()->getConnection()->getConnectionParameters());
        $server = new \OAuth2_Server($storage);
        $server->addGrantType(new \OAuth2_GrantType_AuthorizationCode($storage));
        
        $request = \OAuth2_Request::createFromGlobals();
        $response = new \OAuth2_Response();
        
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }
        
        $server->handleAuthorizeRequest($request, $response, true);
        $url = $response->getHttpHeader('Location');
        $start = strpos($url, 'code=') + 5;
        $code = substr($url, $start, 40);
        
        $result = new JsonModel(array(
            'code' => $code
        ));
        
        return $result;
    }
    
    public function create($data)
    {
        $this->methodNotAllowed();
    }
    
    public function update($id, $data)
    {
        $this->methodNotAllowed();
    }
    
    public function delete($id)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * This is a convenience method to load the usersTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UsersTable
     */
    protected function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
}