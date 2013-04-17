<?php
namespace Wall\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;

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
    
    /**
     * Method to add an item
     *
     * @param int $userId
     * @param string $title
     * @param string $content
     * @return boolean
     */
    public function create($feedId, $title, $content)
    {
        return $this->insert(array(
            'feed_id' => $feedId,
            'title' => $title,
            'content' => $content,
            'created_at' => new Expression('NOW()'),
            'updated_at' => null
        ));
    }
}