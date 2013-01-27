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
use Zend\Http\Client;
use Zend\Http\Request;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $request = new Request();
        $request->setUri('http://zf2-api/wall/tbhot3ww');
        $request->setMethod('GET');
        
        $client = new Client();
        $response = $client->dispatch($request);
        
        if ($response->isSuccess()) {
            $response = \Zend\Json\Decoder::decode($response->getContent());
            return (array)$response;
        }
        
        return array();
    }
}
