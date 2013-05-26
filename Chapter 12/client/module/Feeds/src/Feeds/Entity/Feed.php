<?php

namespace Feeds\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Feeds\Entity\Article;

class Feed
{
    protected $id = null;
    protected $userId = null;
    protected $url = null;
    protected $title = null;
    protected $icon = null;
    protected $articles = array();
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
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    
    public function setArticles($articles)
    {
        $hydrator = new ClassMethods();
        
        foreach ($articles as $a) {
            $this->articles[] = $hydrator->hydrate($a, new Article());
        }
    }
    
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime($createdAt);
    }
    
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime($updatedAt);
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
    
    public function getIcon()
    {
        return $this->icon;
    }
    
    public function getArticles()
    {
        return $this->articles;
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}