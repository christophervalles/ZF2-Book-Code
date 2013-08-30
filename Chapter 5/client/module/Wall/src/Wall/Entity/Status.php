<?php

namespace Wall\Entity;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Stdlib\Hydrator\ClassMethods;

class Status
{
    const COMMENT_TYPE_ID = 1;
    
    protected $id = null;
    protected $userId = null;
    protected $status = null;
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
    
    public function setStatus($status)
    {
        $this->status = $status;
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
    
    public function getStatus()
    {
        return $this->status;
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
    
    public static function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'status',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 65535,
                    ),
                ),
            ),
        )));
        
        return $inputFilter;
    }
}