<?php
namespace Wall\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;

class UserFeedEntriesTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_feed_entries';
    
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
     * Method to get rows by feed_id
     *
     * @param int $feedId
     * @return ArrayObject
     */
    public function getByFeedId($feedId)
    {
        return $this->select(array('feed_id' => $feedId));
    }
    
    /**
     * Method to get rows by id
     *
     * @param int $id
     * @return ArrayObject
     */
    public function getById($id)
    {
        return $this->select(array('id' => $id));
    }
}