<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;

class Image
{
    const COMMENT_TYPE_ID = 2;
    
    public $domain = 'http://zf2-api/images/';
    
    protected $id = null;
    protected $userId = null;
    protected $filename = null;
    protected $comments = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }
    
    public function setFilename($filename)
    {
        $this->filename = $filename;
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
    
    public function getFilename()
    {
        return $this->filename;
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    public function getUrl()
    {
        return $this->domain . $this->getFilename();
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