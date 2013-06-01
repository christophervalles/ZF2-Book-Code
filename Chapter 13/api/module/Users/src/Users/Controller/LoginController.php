<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Crypt\Password\Bcrypt;

/**
 * This class is the responsible to answer the requests to the /wall endpoint
 *
 * @package Wall/Controller
 */
class LoginController extends AbstractRestfulController
{
    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function get($username)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function getList()
    {
        $this->methodNotAllowed();
    }
    
    /**
     * This method inspects the request and routes the data
     * to the correct method
     *
     * @return void
     */
    public function create($data)
    {
        $usersTable = $this->getUsersTable();
        $user = $usersTable->getByUsername($data['username']);
        
        $bcrypt = new Bcrypt();
        if (!empty($user) && $bcrypt->verify($data['password'], $user->password)) {
            $storage = new \OAuth2_Storage_Pdo($usersTable->adapter->getDriver()->getConnection()->getConnectionParameters());
            $server = new \OAuth2_Server($storage);
            $server->addGrantType(new \OAuth2_GrantType_AuthorizationCode($storage));
            $response = $server->handleTokenRequest(\OAuth2_Request::createFromGlobals(), new \OAuth2_Response());
            
            if (!$response->isSuccessful()) {
                $result = new JsonModel(array(
                    'result' => false,
                    'errors' => 'Invalid oauth'
                ));
            }
            
            $result = new JsonModel(array(
                'oauth' => array(
                    'access_token' => $response->getParameter('access_token'),
                    'expires_in' => $response->getParameter('expires_in'),
                    'refresh_token' => $response->getParameter('refresh_token')
                ),
                'errors' => null
            ));
        } else {
            $result = new JsonModel(array(
                'result' => false,
                'errors' => 'Invalid Username or password'
            ));
        }
        
        return $result;
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function update($id, $data)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function delete($id)
    {
        $this->methodNotAllowed();
    }
    
    protected function methodNotAllowed()
    {
        $this->response->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_405);
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