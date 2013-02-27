<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Wall\Entity\Status;

class User
{
    protected $id;
    protected $username;
    protected $name;
    protected $surname;
    protected $bio;
    protected $location;
    protected $gender;
    protected $statuses = array();
    
    public function setId($id)
    {
        $this->id = (int)$id;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    
    public function setBio($bio)
    {
        $this->bio = $bio;
    }
    
    public function setLocation($location)
    {
        $this->location = $location;
    }
    
    public function setGender($gender)
    {
        $this->gender = (int)$gender;
    }
    
    public function setStatuses($statuses)
    {
        $hydrator = new ClassMethods();
        
        foreach ($statuses as $status) {
            $this->statuses[] = $hydrator->hydrate($status, new Status());
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSurname()
    {
        return $this->surname;
    }
    
    public function getBio()
    {
        return $this->bio;
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    
    public function getGender()
    {
        return $this->gender;
    }
    
    public function getGenderString()
    {
        return $this->gender == 1? 'Male' : 'Female';
    }
    
    public function getStatuses()
    {
        return $this->statuses;
    }
}