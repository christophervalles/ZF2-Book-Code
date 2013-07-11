<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Wall\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Http\Client;
use Zend\Filter\FilterChain;
use Zend\Filter\StripTags;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewLines;
use Zend\Dom\Query;

/**
 * This class is the responsible to answer the requests to the /wall endpoint
 *
 * @package Wall/Controller
 */
class IndexController extends AbstractRestfulController
{
    /**
     * Holds the table object
     *
     * @var UsersTable
     */
    protected $usersTable;
    
    /**
     * Holds the table object
     *
     * @var UserStatusesTable
     */
    protected $userStatusesTable;
    
    /**
     * Holds the table object
     *
     * @var UserImagesTable
     */
    protected $userImagesTable;
    
    /**
     * Holds the table object
     *
     * @var UserLinksTable
     */
    protected $userLinksTable;
    
    /**
     * This method will fetch the data related to the wall of a user and return
     * it. The data is fetched using the username as reference
     *
     * @param string $username 
     * @return array
     */
    public function get($username)
    {
        $usersTable = $this->getUsersTable();
        $userStatusesTable = $this->getUserStatusesTable();
        $userImagesTable = $this->getUserImagesTable();
        $userLinksTable = $this->getUserLinksTable();
        
        $userData = $usersTable->getByUsername($username);
        $userStatuses = $userStatusesTable->getByUserId($userData->id);
        $userImages = $userImagesTable->getByUserId($userData->id);
        $userLinks = $userLinksTable->getByUserId($userData->id);
        
        $wallData = $userData->getArrayCopy();
        $wallData['feed'] = array_merge(
            $userStatuses->toArray(), 
            $userImages->toArray(),
            $userLinks->toArray()
        );
        
        usort($wallData['feed'], function($a, $b){
            $timestampA = strtotime($a['created_at']);
            $timestampB = strtotime($b['created_at']);
            
            if ($timestampA == $timestampB) {
                return 0;
            }
            
            return ($timestampA > $timestampB) ? -1 : 1;
        });
        
        if ($userData !== false) {
            return new JsonModel($wallData);
        } else {
            throw new \Exception('User not found', 404);
        }
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
        if (array_key_exists('status', $data) && !empty($data['status'])) {
            $result = $this->createStatus($data);
        }
        
        if (array_key_exists('image', $data) && !empty($data['image'])) {
            $result = $this->createImage($data);
        }
        
        if (array_key_exists('url', $data) && !empty($data['url'])) {
            $result = $this->createLink($data);
        }
        
        return $result;
    }
    
    /**
     * Handle the creation of a new image
     *
     * @param array $data 
     * @return JsonModel
     */
    protected function createImage($data)
    {
        $userImagesTable = $this->getUserImagesTable();
        
        $filters = $userImagesTable->getInputFilter();
        $filters->setData($data);
        
        if ($filters->isValid()) {
            $filename = sprintf('public/images/%s.png', sha1(uniqid(time(), TRUE)));
            $content = base64_decode($data['image']);
            $image = imagecreatefromstring($content);
            
            if (imagepng($image, $filename) === TRUE) {
                $result = new JsonModel(array(
                    'result' => $userImagesTable->create($data['user_id'], basename($filename))
                ));
            } else {
                $result = new JsonModel(array(
                    'result' => false,
                    'errors' => 'Error while storing the image'
                ));
            }
            imagedestroy($image);
        } else {
            $result = new JsonModel(array(
                'result' => false,
                'errors' => $filters->getMessages()
            ));
        }
        
        return $result;
    }
    
    /**
     * Handle the creation of a new status
     *
     * @param array $data 
     * @return JsonModel
     */
    protected function createStatus($data)
    {
        $userStatusesTable = $this->getUserStatusesTable();
        
        $filters = $userStatusesTable->getInputFilter();
        $filters->setData($data);
        
        if ($filters->isValid()) {
            $data = $filters->getValues();
            
            $result = new JsonModel(array(
                'result' => $userStatusesTable->create($data['user_id'], $data['status'])
            ));
        } else {
            $result = new JsonModel(array(
                'result' => false,
                'errors' => $filters->getMessages()
            ));
        }
        
        return $result;
    }
    
    /**
     * Handle the creation of a new link
     *
     * @param array $data 
     * @return JsonModel
     */
    protected function createLink($data)
    {
        $userLinksTable = $this->getUserLinksTable();
        
        $filters = $userLinksTable->getInputFilter();
        $filters->setData($data);
        
        if ($filters->isValid()) {
            $data = $filters->getValues();
            
            $client = new Client($data['url']);
            $client->setEncType(Client::ENC_URLENCODED);
            $client->setMethod(\Zend\Http\Request::METHOD_GET);
            $response = $client->send();
            
            if ($response->isSuccess()) {
                $html = $response->getBody();
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 
                
                $dom = new Query($html);
                $title = $dom->execute('title')->current()->nodeValue;
                
                if (!empty($title)) {
                    $filterChain = new FilterChain();
                    $filterChain->attach(new StripTags());
                    $filterChain->attach(new StringTrim());
                    $filterChain->attach(new StripNewLines());
                    
                    $title = $filterChain->filter($title);
                } else {
                    $title = NULL;
                }
                
                return new JsonModel(array(
                    'result' => $userLinksTable->create(
                        $data['user_id'], 
                        $data['url'], 
                        $title
                    )
                ));
            }
        }
        
        return new JsonModel(array(
            'result' => false,
            'errors' => $filters->getMessages()
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
            $this->usersTable = $sm->get('Wall\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    /**
     * This is a convenience method to load the userStatusesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserStatusesTable
     */
    protected function getUserStatusesTable()
    {
        if (!$this->userStatusesTable) {
            $sm = $this->getServiceLocator();
            $this->userStatusesTable = $sm->get('Wall\Model\UserStatusesTable');
        }
        return $this->userStatusesTable;
    }
    
    /**
     * This is a convenience method to load the userImagesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserStatusesTable
     */
    protected function getUserImagesTable()
    {
        if (!$this->userImagesTable) {
            $sm = $this->getServiceLocator();
            $this->userImagesTable = $sm->get('Wall\Model\UserImagesTable');
        }
        return $this->userImagesTable;
    }
    
    /**
     * This is a convenience method to load the userLinksTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserStatusesTable
     */
    protected function getUserLinksTable()
    {
        if (!$this->userLinksTable) {
            $sm = $this->getServiceLocator();
            $this->userLinksTable = $sm->get('Wall\Model\UserLinksTable');
        }
        return $this->userLinksTable;
    }
}