<?php
namespace Wall\Model;

class User
{
    /**
     * Properties of a User
     */
    protected $id;
    protected $username;
    protected $email;
    protected $avatar_id;
    protected $name;
    protected $surname;
    protected $bio;
    protected $location;
    protected $gender;
    
    /**
     * This method is called by the TableGateway to populate the object
     *
     * @param array $data 
     * @return void
     */
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
    
    /**
     * Helper function to get a string representation of the gender
     *
     * @param int $gender 
     * @return string
     */
    public function getGenderString($gender)
    {
        return $gender == 1? 'Male' : 'Female';
    }
}