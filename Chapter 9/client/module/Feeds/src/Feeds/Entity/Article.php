<?php

namespace Feeds\Entity;

class Article
{
    protected $id = null;
    protected $feedId = null;
    protected $title = null;
    protected $content = null;
    protected $url = null;
    protected $author = null;
    protected $readed = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function setFeedId($feedId)
    {
        $this->feedId = (int)$feedId;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
    public function setReaded($readed)
    {
        $this->readed = $readed;
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
    
    public function getFeedId()
    {
        return $this->feedId;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    
    public function getReaded()
    {
        return $this->readed;
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