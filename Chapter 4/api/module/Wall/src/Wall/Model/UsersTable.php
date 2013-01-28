<?php
namespace Wall\Model;

use Wall\Model\User;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\Feature\FeatureSet;

class UsersTable extends AbstractTableGateway
{
    protected $table = 'users';
    
    /**
     * Constructor
     *
     * @param string $table
     * @param Adapter $adapter
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     * @param ResultSetInterface $resultSetPrototype
     * @param Sql $sql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(Adapter $adapter)
    {
        // adapter
        $this->adapter = $adapter;
        
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(
            new User(
                'id', 
                $this, 
                $adapter, 
                $this->sql
            )
        );
        $features = new Feature\FeatureSet(array($features));
        
        // result prototype
        $this->resultSetPrototype = ($resultSetPrototype) ?: new ResultSet;
        
        // Sql object (factory for select, insert, update, delete)
        $this->sql = ($sql) ?: new Sql($this->adapter, $this->table);
        
        // check sql object bound to same table
        if ($this->sql->getTable() != $this->table) {
            throw new Exception\InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }
        
        $this->initialize();
    }
    
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    
    /**
     * Method to get users by username
     *
     * @param string $username
     * @return User
     */
    public function getByUsername($username)
    {
        $rowset = $this->select(array('username' => $username));
        
        return $rowset->current();
    }
}