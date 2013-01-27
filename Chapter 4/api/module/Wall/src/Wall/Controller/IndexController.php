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

class IndexController extends AbstractRestfulController
{
    protected $usersTable;
    
    public function get($id)
    {
        $usersTable = $this->getUsersTable();
        $user = $usersTable->getById(1);
        
        return array(
            'user' => $user
        );
    }
    
    public function getList(){
    }
    public function create($data){
    }
    public function update($id, $data){
    }
    public function delete($id){
    }
    
    public function getUsersTable()
    {
        if (!$this->usersTable) {
            $sm = $this->getServiceLocator();
            $this->usersTable = $sm->get('Wall\Model\UsersTable');
        }
        return $this->usersTable;
    }
}
