<?php

namespace Common\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Api\Client\ApiClient;
use Users\Entity\User;
use Zend\Stdlib\Hydrator\ClassMethods;

class Api implements AdapterInterface
{
    /**
     * Holds the credentials
     *
     * @var string
     */
    private $username = null;
    private $password = null;
    
    
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        $result = ApiClient::authenticate(array(
            'username' => $this->username,
            'password' => $this->password
        ));
        
        if ($result['result'] === true) {
            $hydrator = new ClassMethods();
            $user = $hydrator->hydrate(ApiClient::getUser($this->username), new User());
            
            $response = new Result(Result::SUCCESS, $user, array('Authentication successful.'));
        } else {
            $response = new Result(Result::FAILURE, NULL , array('Invalid credentials.'));
        }
        
        return $response;
    }
}