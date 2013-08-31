<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Crypt\Password\Bcrypt;

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
     * @var UserImagesTable
     */
    protected $userImagesTable;
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function get($username)
    {
        $usersTable = $this->getUsersTable();
        $userImagesTable = $this->getUserImagesTable();
        
        $userData = $usersTable->getByUsername($username);
        
        if ($userData !== false) {
            $userData['avatar'] = $userImagesTable->getById($userData['avatar_id']);
            return new JsonModel($userData);
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
    public function create($unfilteredData)
    {
        $usersTable = $this->getUsersTable();
        
        $filters = $usersTable->getInputFilter();
        $filters->setData($unfilteredData);
        
        if ($filters->isValid()) {
            $data = $filters->getValues();
            
            $avatarContent = array_key_exists('avatar', $unfilteredData) ? $unfilteredData['avatar'] : NULL;
            
            $bcrypt = new Bcrypt();
            $data['password'] = $bcrypt->create($data['password']);
            
            if ($usersTable->create($data)) {
                if (!empty($avatarContent)) {
                    $userImagesTable = $this->getUserImagesTable();
                    $user = $usersTable->getByUsername($data['username']);
                    
                    $filename = sprintf('public/images/%s.png', sha1(uniqid(time(), TRUE)));
                    $content = base64_decode($avatarContent);
                    $image = imagecreatefromstring($content);
                    
                    if (imagepng($image, $filename) === TRUE) {
                        $userImagesTable->create($user['id'], basename($filename));
                    }
                    imagedestroy($image);
                    
                    $image = $userImagesTable->getByFilename(basename($filename));
                    $usersTable->updateAvatar($image['id'], $user['id']);
                }
                
                $result = new JsonModel(array(
                    'result' => true
                ));
            } else {
                $result = new JsonModel(array(
                    'result' => false
                )); 
            }
        } else {
            $result = new JsonModel(array(
                'result' => false,
                'errors' => $filters->getMessages()
            ));
        }
        
        return $result;
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
            $this->usersTable = $sm->get('Users\Model\UsersTable');
        }
        return $this->usersTable;
    }
    
    /**
     * This is a convenience method to load the userImagesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserImagesTable
     */
    protected function getUserImagesTable()
    {
        if (!$this->userImagesTable) {
            $sm = $this->getServiceLocator();
            $this->userImagesTable = $sm->get('Users\Model\UserImagesTable');
        }
        return $this->userImagesTable;
    }
}