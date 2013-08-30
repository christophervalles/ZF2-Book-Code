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
     * Holds the table object
     *
     * @var UserStatusesTable
     */
    protected $userStatusesTable;
    
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
        
        $userData = $usersTable->getByUsername($username);
        $userStatuses = $userStatusesTable->getByUserId($userData->id)->toArray();
        
        $wallData = $userData->getArrayCopy();
        $wallData['feed'] = $userStatuses;
        
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
     * This is a convenience method to load the userStatusesTable db object and keeps track
     * of the instance to avoid multiple of them
     *
     * @return UserStatusesTable
     */
    protected function getUserStatusesTable()
    {
        if (!$this->userStatusesTable) {
            $sm = $this->getServiceLocator();
            $this->userStatusesTable = $sm->get('Users\Model\UserStatusesTable');
        }
        return $this->userStatusesTable;
    }
}