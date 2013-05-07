<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class UsersTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'users';
    
    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
    
    /**
     * Method to get users by username
     *
     * @param string $username
     * @return ArrayObject
     */
    public function getByUsername($username)
    {
        $rowset = $this->select(array('username' => $username));
        
        return $rowset->current();
    }
    
    /**
     * Method to get users by id
     *
     * @param int $id
     * @return ArrayObject
     */
    public function getById($id)
    {
        $rowset = $this->select(array('id' => $id));
        
        return $rowset->current();
    }
    
    /**
     * Method to create a new user on the DB
     *
     * @param array $userData
     * @return int
     */
    public function create($userData)
    {
        $userData['created_at'] = new Expression('NOW()');
        
        return $this->insert($userData);
    }
    
    /**
     * Update the avatar of a user
     *
     * @param int $imageId
     * @param int $userId
     * @return int
     */
    public function updateAvatar($imageId, $userId)
    {
        return $this->update(array(
            'avatar_id' => $imageId
        ), array(
            'id' => $userId)
        );
    }
    
    /**
     * Return a configured input filter to be able to validate and
     * filter the data.
     *
     * @return InputFilter
     */
    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'username',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 50
                    ),
                ),
                array(
                    'name' => 'Zend\Validator\Db\NoRecordExists',
                    'options' => array(
                        'table' => 'users',
                        'field' => 'username',
                        'adapter' => $this->adapter
                    )
                )
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'email',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 6,
                        'max' => 254
                    ),
                ),
                array(
                    'name' => 'EmailAddress',
                ),
                array(
                    'name' => 'Zend\Validator\Db\NoRecordExists',
                    'options' => array(
                        'table' => 'users',
                        'field' => 'email',
                        'adapter' => $this->adapter
                    )
                )
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'password',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'name',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 25
                    )
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'surname',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                ),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 50
                    )
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'bio',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'location',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'gender',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => array('0', '1')
                    )
                ),
            ),
        )));
        
        return $inputFilter;
    }
}