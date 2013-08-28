<?php

namespace Wall\Entity;

class User
{
    const GENDER_MALE = 1;
    
    protected $id;
    protected $username;
    protected $name;
    protected $surname;
    protected $bio;
    protected $location;
    protected $gender;
    
    public function setId($id)
    {
        $this->id = $id;
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
        $this->gender = $gender;
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
        return $this->gender == self::GENDER_MALE? 'Male' : 'Female';
    }
}