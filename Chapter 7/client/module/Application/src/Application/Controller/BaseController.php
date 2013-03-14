<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client;

class BaseController extends AbstractActionController {
    
    /**
     * Make a POST request to the API
     *
     * @param string $url 
     * @param array $data 
     * @return Response
     */
    protected function makePostRequest($url, $data)
    {
        $client = new Client($url);
        $client->setEncType(Client::ENC_URLENCODED);
        $client->setMethod(\Zend\Http\Request::METHOD_POST);
        $client->setParameterPost($data);
        
        return $client->send();
    }
}
