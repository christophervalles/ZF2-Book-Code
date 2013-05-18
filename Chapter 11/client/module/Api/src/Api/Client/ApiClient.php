<?php

namespace Api\Client;

use Zend\Http\Client as Client;
use Zend\Http\Request as Request;
use Zend\Json\Decoder as JsonDecoder;
use Zend\Json\Json as Json;

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
    protected static $endpointFeeds = '/api/feeds/%s';
    protected static $endpointSpecificFeed = '/api/feeds/%s/%d';
    protected static $endpointUsers = '/api/users';
    protected static $endpointGetUser = '/api/users/%s';
    
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
        return self::doRequest($url, $data, Request::METHOD_POST);
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
        return self::doRequest($url);
    }
    
    /**
     * Perform an API request to add a new subscription
     *
     * @param string $username 
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function addFeedSubscription($username, $postData)
    {
        $url = self::$endpointHost . sprintf(self::$endpointFeeds, $username);
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }
    
    /**
     * Perform an API request to remove a subscription
     *
     * @param string $username 
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function removeFeedSubscription($username, $feedId)
    {
        $url = self::$endpointHost . sprintf(self::$endpointSpecificFeed, $username, $feedId);
        return self::doRequest($url, null, Request::METHOD_DELETE);
    }
    
    /**
     * Perform an API request to add a new user
     *
     * @param array $postData
     * @return Zend\Http\Response
     */
    public static function registerUser($postData)
    {
        $url = self::$endpointHost . self::$endpointUsers;
        return self::doRequest($url, $postData, Request::METHOD_POST);
    }
    
    /**
     * Perform an API request to get the basic data of a user
     *
     * @param string $username
     * @return Zend\Http\Response
     */
    public static function getUser($username)
    {
        $url = self::$endpointHost . sprintf(self::$endpointGetUser, $username);
        return self::doRequest($url, null, Request::METHOD_GET);
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
    protected static function doRequest($url, array $postData = null, $method = Request::METHOD_GET)
    {
        $client = self::getClientInstance();
        $client->setUri($url);
        $client->setMethod($method);
        
        if ($postData !== null) {
            $client->setParameterPost($postData);
        }
        
        $response = $client->send();
        
        if ($response->isSuccess()) {
            return JsonDecoder::decode($response->getBody(), Json::TYPE_ARRAY);
        } else {
            return FALSE;
        }
    }
}