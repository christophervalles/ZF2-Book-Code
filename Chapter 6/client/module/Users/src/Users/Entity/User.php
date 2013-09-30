<?php

namespace Users\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Wall\Entity\Link;
use Wall\Entity\Image;
use Wall\Entity\Status;

class User
{
    const GENDER_MALE = 1;
    
    protected $id;
    protected $username;
    protected $name;
    protected $surname;
    protected $avatar;
    protected $bio;
    protected $location;
    protected $gender;
    protected $createdAt = null;
    protected $updatedAt = null;
    protected $feed = array();
    
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
    
    public function setAvatar($avatar)
    {
        if (empty($avatar)) {
            $defaultImage = new Image();
            $defaultImage->setFilename('default.png');
            $this->avatar = $defaultImage;
        } else {
            $hydrator = new ClassMethods();
            $this->avatar = $hydrator->hydrate($avatar, new Image());
        }
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
    
    public function setFeed($feed)
    {
        $hydrator = new ClassMethods();
        
        foreach ($feed as $entry) {
            if (array_key_exists('status', $entry)) {
                $this->feed[] = $hydrator->hydrate($entry, new Status());
            } else if (array_key_exists('filename', $entry)) {
                $this->feed[] = $hydrator->hydrate($entry, new Image());
            }
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
    
    public function getAvatar()
    {
        return $this->avatar;
    }
    
    public function getGenderString()
    {
        return $this->gender == self::GENDER_MALE? 'Male' : 'Female';
    }
    
    public function getFeed()
    {
        return $this->feed;
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