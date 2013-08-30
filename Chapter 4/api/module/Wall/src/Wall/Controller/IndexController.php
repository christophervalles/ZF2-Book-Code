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
     * This method will fetch the data related to the wall of a user and return
     * it. The data is fetched using the username as reference
     *
     * @param string $username 
     * @return array
     */
    public function get($username)
    {
        $usersTable = $this->getUsersTable();
        $userData = $usersTable->getByUsername($username);
        $wallData = $userData->getArrayCopy();
        
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
     * Method not available for this endpoint
     *
     * @return void
     */
    public function create($data)
    {
        $this->methodNotAllowed();
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
}