<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Client\ApiClient as ApiClient;

class IndexController extends AbstractActionController
{
    /**
     * Get the feed list and the posts of the feed we are looking at now
     *
     * @return void
     * @author Christopher
     */
    public function indexAction()
    {
        $username = $this->params()->fromRoute('username');
        $feeds = $response = ApiClient::getFeeds($username);
        die(var_dump($this->params()->fromRoute('feed_id')));
    }
}