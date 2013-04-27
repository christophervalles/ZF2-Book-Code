<?php

namespace Api\Client;

use Zend\Http\Client as Client;
use Zend\Http\Request as Request;

/**
 * This client manages all the operations needed to interface with the
 * social network API
 *
 * @package default
 */
class ApiClient {
    
    /**
     * Holds the client we will reuse in this class
     *
     * @var Client
     */
    protected static $client = null;
    
    /**
     * Holds the endpoint urls
     *
     * @var string
     */
    protected static $endpointHost = 'http://zf2-api';
    protected static $endpointWall = '/api/wall/%s';
    protected static $endpointFeeds = '/api/wall/%s';
    
    /**
     * Perform an API reqquest to retrieve the data of the wall
     * of an specific user on the social network
     *
     * @param string $username
     * @return Zend\Http\Response
     */
    public static function getWall($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointWall, $username);
        return self::doRequest($url);
    }
    
    /**
     * Perform an API request to post content on the wall of an specific user
     *
     * @param string $username 
     * @param array $data 
     * @return Zend\Http\Response
     */
    public static function postWallContent($username, $data)
    {
        $url = self::$endpointHost . sprintf(self::$endpointWall, $username);
        return self::doRequest($url, $data);
    }
    
    /**
     * Perform an API request to get the list subscriptions of a username
     *
     * @param string $username 
     * @return Zend\Http\Response
     */
    public static function getFeeds($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointFeeds, $username);
        return self::doRequest($url, $data);
    }
    
    /**
     * Create a new instance of the Client if we don't have it or 
     * return the one we already have to reuse
     *
     * @return Client
     */
    protected static function getClientInstance()
    {
        if (self::$client === null) {
            self::$client = new Client();
            self::$client->setEncType(Client::ENC_URLENCODED);
        }
        
        return self::$client;
    }
    
    /**
     * Perform a request to the API
     *
     * @param string $url
     * @param array $postData
     * @param Client $client
     * @return Zend\Http\Response
     * @author Christopher
     */
    protected static function doRequest($url, array $postData = null, Client $client = null)
    {
        if ($client === null) {
            $client = self::getClientInstance();
        }
        
        $client->setUri($url);
        $client->setMethod(Request::METHOD_GET);
        
        if ($postData !== null) {
            $client->setMethod(Request::METHOD_POST);
            $client->setParameterPost($postData);
        }
        
        return $client->send();
    }
}