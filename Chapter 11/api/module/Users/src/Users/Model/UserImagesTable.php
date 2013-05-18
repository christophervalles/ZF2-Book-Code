<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class UserImagesTable extends AbstractTableGateway implements AdapterAwareInterface
{
    /**
     * Hold the entity id used for comments
     */
    const COMMENT_TYPE_ID = 2;
    
    /**
     * Hold the table name
     *
     * @var string
     */
    protected $table = 'user_images';
    const TABLE_NAME = 'user_images';
    
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
     * Method to get an image by filename
     *
     * @param string $filename
     * @return ArrayObject
     */
    public function getByFilename($filename)
    {
        $rowset = $this->select(array('filename' => $filename));
        return $rowset->current();
    }
    
    /**
     * Method to get an image by id
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
     * Method to insert an entry
     *
     * @param int $userId
     * @param string $filename
     * @return boolean
     */
    public function create($userId, $filename)
    {
        return $this->insert(array(
            'user_id' => $userId,
            'filename' => $filename,
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
        
        return $inputFilter;
    }
}