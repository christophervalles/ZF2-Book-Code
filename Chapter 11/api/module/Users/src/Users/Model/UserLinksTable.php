<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Users\Validator\Url;

class UserLinksTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /**
     * Hold the entity id used for comments
     */
    const COMMENT_TYPE_ID = 3;
    
    /**
     * Hold the table name
     *
     * @var string
     */
    protected $table = 'user_links';
    const TABLE_NAME = 'user_links';
    
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
     * Method to get entries by userId
     *
     * @param int $userId
     * @return Zend\Db\ResultSet\ResultSet
     */
    public function getByUserId($userId)
    {
        $select = $this->sql->select()->where(array('user_id' => $userId))->order('created_at DESC');
        return $this->selectWith($select);
    }
    
    /**
     * Method to insert an entry
     *
     * @param int $userId
     * @param string $url
     * @param string $title
     * @return boolean
     */
    public function create($userId, $url, $title)
    {
        return $this->insert(array(
            'user_id' => $userId,
            'url' => $url,
            'title' => $title,
            'created_at' => new Expression('NOW()'),
            'updated_at' => null
        ));
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
            'name'     => 'user_id',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'Int'),
            ),
            'validators' => array(
                array('name' => 'NotEmpty'),
                array('name' => 'Digits'),
                array(
                    'name' => 'Zend\Validator\Db\RecordExists',
                    'options' => array(
                        'table' => 'users',
                        'field' => 'id',
                        'adapter' => $this->adapter
                    )
                )
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'url',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array('name' => 'NotEmpty'),
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'max' => 2048
                    )
                ),
                array('name' => '\Users\Validator\Url'),
            ),
        )));
        
        return $inputFilter;
    }
}