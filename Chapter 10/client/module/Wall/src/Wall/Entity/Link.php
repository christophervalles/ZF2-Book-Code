<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;

class Link
{
    const COMMENT_TYPE_ID = 3;
    
    protected $id = null;
    protected $userId = null;
    protected $url = null;
    protected $title = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    protected $comments = null;
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime($createdAt);
    }
    
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime($updatedAt);
    }
    
    public function setComments($comments)
    {
        $hydrator = new ClassMethods();
      
        foreach ($comments as $c) {
            $this->comments[] = $hydrator->hydrate($c, new Comment());
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUserId()
    {
        return $this->userId;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    public function getComments()
    {
        return $this->comments;
    }
    
    public function getType()
    {
        return self::COMMENT_TYPE_ID;
    }
}