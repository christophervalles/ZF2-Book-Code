<?php
namespace Wall\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class UsersTable
{
    /**
     * Holds the TableGateway object
     *
     * @var TableGateway
     */
    protected $tableGateway;
    
    /**
     * Constructor with a TableGateway object injected
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Method to get users by username
     *
     * @param string $username
     * @return User
     */
    public function getByUsername($username)
    {
        $rowset = $this->tableGateway->select(array('username' => $username));
        
        return $rowset->current();
    }
}