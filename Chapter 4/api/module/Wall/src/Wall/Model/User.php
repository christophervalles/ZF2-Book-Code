<?php
namespace Wall\Model;

class User
{
    public $id;
    public $username;
    public $email;
    public $avatar_id;
    public $name;
    public $surname;
    public $bio;
    public $location;
    public $gender;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->avatar_id = (isset($data['avatar_id'])) ? $data['avatar_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
        $this->bio = (isset($data['bio'])) ? $data['bio'] : null;
        $this->location = (isset($data['location'])) ? $data['location'] : null;
        $this->gender = (isset($data['gender'])) ? $this->getGenderString($data['gender']) : null;
    }
    
    public function getGenderString($gender)
    {
        return $gender == 1? 'Male' : 'Female';
    }
}