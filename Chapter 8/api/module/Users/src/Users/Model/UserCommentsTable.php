<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Expression;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class UserCommentsTable extends AbstractTableGateway implements AdapterAwareInterface
{
    protected $table = 'user_comments';
    
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
     * Method to insert an entry
     *
     * @param int $userId
     * @param int $type
     * @param int $entryId
     * @param string $comment
     * @return boolean
     */
    public function create($userId, $type, $entryId, $comment)
    {
        return $this->insert(array(
            'user_id' => $userId,
            'type' => $type,
            'entry_id' => $entryId,
            'comment' => $comment,
            'created_at' => new Expression('NOW()'),
            'updated_at' => null
        ));
    }
    
    /**
     * Method to get entries by type and entry_id
     *
     * @param int $type
     * @param int $entryId
     * @return Zend\Db\ResultSet\ResultSet
     */
    public function getByTypeAndEntryId($type, $entryId)
    {
        $select = $this->sql->select()->where(array('type' => $type, 'entry_id' => $entryId))->order('created_at ASC');
        return $this->selectWith($select);
    }
    
    /**
     * Return a configured input filter to be able to validate and
     * filter the data.
     *
     * @return InputFilter
     */
    public function getInputFilter($validatorTable, $config)
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
            'name'     => 'type',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array('name' => 'NotEmpty'),
                array('name' => 'Digits'),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'entry_id',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array('name' => 'NotEmpty'),
                array('name' => 'Digits'),
                array(
                    'name' => 'Zend\Validator\Db\RecordExists',
                    'options' => array(
                        'table' => $validatorTable,
                        'field' => 'id',
                        'adapter' => $this->adapter
                    )
                )
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'comment',
            'required' => true,
            'validators' => array(
                array('name' => 'NotEmpty'),
                array(
                    'name' => '\Users\Validator\Spam',
                    'options' => array(
                        'apiKey' => $config['apiKey'],
                        'url' => $config['url']
                    )
                ),
            ),
        )));
        
        return $inputFilter;
    }
}