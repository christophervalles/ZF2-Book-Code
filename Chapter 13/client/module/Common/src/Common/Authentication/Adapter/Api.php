<?php

namespace Common\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Api\Client\ApiClient;
use Users\Entity\User;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Session\Container;

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
        $session = new Container('oauth');
        $session->setExpirationSeconds(30);
        
        if ($session->authorizationCode === null) {
            $oauthCode = ApiClient::getOAuthAuthorizationCode();
            $session->authorizationCode = $oauthCode['code'];
        }
        
        $result = ApiClient::authenticate(array(
            'username' => $this->username,
            'password' => $this->password,
            'code' => $session->authorizationCode
        ));
        
        if (array_key_exists('oauth', $result)) {
            $hydrator = new ClassMethods();
            $user = $hydrator->hydrate(ApiClient::getUser($this->username), new User());
            
            $session = new Container('oauth_session');
            $session->setExpirationSeconds($result['oauth']['expires_in']);
            $session->accessToken = $result['oauth']['access_token'];
            $session->refreshToken = $result['oauth']['refresh_token'];
            
            $response = new Result(Result::SUCCESS, $user, array('Authentication successful.'));
        } else {
            $response = new Result(Result::FAILURE, NULL , array('Invalid credentials.'));
        }
        
        return $response;
    }
}