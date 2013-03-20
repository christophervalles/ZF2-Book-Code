<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;

class Comment
{
    protected $id = null;
    protected $user = null;
    protected $comment = null;
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function setUser($user)
    {
        $hydrator = new ClassMethods();
        
        $this->user = $hydrator->hydrate($user, new User());
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getComment()
    {
        return $this->comment;
    }
}