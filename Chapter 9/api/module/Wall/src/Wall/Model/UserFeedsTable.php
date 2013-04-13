<?php
namespace Wall\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;

class UserFeeds extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_feeds';
    
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
     * Method to get rows by user_id
     *
     * @param int $id
     * @return ArrayObject
     */
    public function getByUserId($userId)
    {
        return $this->select(array('user_id' => $userId));
    }
}