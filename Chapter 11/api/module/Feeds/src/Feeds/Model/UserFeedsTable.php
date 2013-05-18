<?php
namespace Feeds\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;

class UserFeedsTable extends AbstractTableGateway implements AdapterAwareInterface
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
    
    /**
     * Method to add a subscription to a rss feed
     *
     * @param int $userId
     * @param string $url
     * @param string $title
     * @return boolean
     */
    public function create($userId, $url, $title, $icon)
    {
        return $this->insert(array(
            'user_id' => $userId,
            'url' => $url,
            'title' => $title,
            'icon' => $icon,
            'created_at' => new Expression('NOW()'),
            'updated_at' => null
        ));
    }
    
    /**
     * Update the feed updated_at field to reflect when we get the entries
     *
     * @param int $feedId 
     * @return int
     */
    public function updateTimestamp($feedId)
    {
        return $this->update(array(
            'updated_at' => new Expression('NOW()')
        ), array(
            'id' => $feedId)
        );
    }
}