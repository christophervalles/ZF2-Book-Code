<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;

class UsersTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'users';
    
    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    
    /**
     * Method to get users by username
     *
     * @param string $username
     * @return ArrayObject
     */
    public function getByUsername($username)
    {
        $rowset = $this->select(array('username' => $username));
        
        return $rowset->current();
    }
    
    /**
     * Method to get users by id
     *
     * @param int $id
     * @return ArrayObject
     */
    public function getById($id)
    {
        $rowset = $this->select(array('id' => $id));
        
        return $rowset->current();
    }
    
    /**
     * Update the avatar of a user
     *
     * @param int $imageId
     * @param int $userId
     * @return int
     */
    public function updateAvatar($imageId, $userId)
    {
        return $this->update(array(
            'avatar_id' => $imageId
        ), array(
            'id' => $userId)
        );
    }
}