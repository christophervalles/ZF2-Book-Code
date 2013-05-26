<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feeds\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Feed\Reader\Reader;
use Zend\Http\Client;

/**
 * This class is the responsible to answer the requests to the /feeds endpoint
 *
 * @package Wall/Controller
 */
class IndexController extends AbstractRestfulController
{
    /**
     * Hold the table instance
     *
     * @var UserFeedsTable
     */
    protected $userFeedsTable;
    
    /**
     * Hold the table instance
     *
     * @var UserFeedArticlesTable
     */
    protected $userFeedArticlesTable;
    
    /**
     * Hold the table instance
     *
     * @var UsersTable
     */
    protected $usersTable;
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function get($id)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Return a list of feed subscription for a specific user
     *
     * @return JsonModel
     */
    public function getList()
    {
        $username = $this->params()->fromRoute('username');
        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedArticlesTable = $this->getTable('UserFeedArticlesTable');
        
        $feedsFromDb = $userFeedsTable->getByUserId($user->id)->toArray();
        $feeds = array();
        foreach ($feedsFromDb as $f) {
            $feeds[$f['id']] = $f;
            $feeds[$f['id']]['articles'] = $userFeedArticlesTable->getByFeedId($f['id'])->toArray();
        }
        
        return new JsonModel($feeds);
    }
    
    /**
     * Add a new subscription
     *
     * @return JsonModel
     */
    public function create($data)
    {
        $username = $this->params()->fromRoute('username');
        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);
        
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $rssLinkXpath = '//link[@type="application/rss+xml"]';
        $faviconXpath = '//link[@rel="shortcut icon"]';
        
        $client = new Client($data['url']);
        $client->setEncType(Client::ENC_URLENCODED);
        $client->setMethod(\Zend\Http\Request::METHOD_GET);
        $response = $client->send();
        
        if ($response->isSuccess()) {
            $html = $response->getBody();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 
            
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);
            
            $rssUrl = $xpath->query($rssLinkXpath);
            if ($rssUrl->length == 0) {
                throw new Exception('Rss url not found in the url provided', 404);
            }
            $rssUrl = $rssUrl->item(0)->getAttribute('href');
            
            $faviconUrl = $xpath->query($faviconXpath);
            if ($faviconUrl->length > 0) {
                $faviconUrl = $faviconUrl->item(0)->getAttribute('href');
            } else {
                $faviconUrl = null;
            }
        } else {
            throw new Exception("Website not found", 404);
        }
        
        $rss = Reader::import($rssUrl);
        
        return new JsonModel(array(
            'result' => $userFeedsTable->create($user->id, $rssUrl, $rss->getTitle(), $faviconUrl)
        ));
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
     * Delete a subscription
     *
     * @return JsonModel
     */
    public function delete($id)
    {
        $username = $this->params()->fromRoute('username');
        $usersTable = $this->getTable('UsersTable');
        $user = $usersTable->getByUsername($username);
        
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedArticlesTable = $this->getTable('UserFeedArticlesTable');
        
        $userFeedArticlesTable->delete(array('feed_id' => $id));
        return new JsonModel(array(
            'result' => $userFeedsTable->delete(array('id' => $id, 'user_id' => $user->id))
        ));
    }
    
    protected function methodNotAllowed()
    {
        $this->response->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_405);
    }
    
    protected function getTable($table)
    {
        $sm = $this->getServiceLocator();
        
        switch ($table) {
            case 'UserFeedsTable':
                if (!$this->userFeedsTable) {
                    $this->userFeedsTable = $sm->get('Feeds\Model\UserFeedsTable');
                }
                
                return $this->userFeedsTable;
            case 'UserFeedArticlesTable':
                if (!$this->userFeedArticlesTable) {
                    $this->userFeedArticlesTable = $sm->get('Feeds\Model\UserFeedArticlesTable');
                }
                
                return $this->userFeedArticlesTable;
            case 'UsersTable':
                if (!$this->usersTable) {
                    $this->usersTable = $sm->get('Users\Model\UsersTable');
                }
                
                return $this->usersTable;
        }
    }
}