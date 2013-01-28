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
use Zend\Stdlib\Hydrator\ClassMethods;
use Wall\Entity\User;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $username = $this->getEvent()->getRouteMatch()->getParam('username');
        $request = new Request();
        $request->setUri(sprintf('http://zf2-api/wall/%s', $username));
        $request->setMethod('GET');
        
        $client = new Client();
        $response = $client->dispatch($request);
        
        if ($response->isSuccess()) {
            $response = \Zend\Json\Decoder::decode($response->getContent());
            $hydrator = new ClassMethods(false);
            
            return array(
                'user' => $hydrator->hydrate((array)$response->user, new User())
            );
        }
        
        return array();
    }
}