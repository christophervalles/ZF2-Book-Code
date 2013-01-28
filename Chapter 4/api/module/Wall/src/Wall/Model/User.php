<?php
namespace Wall\Model;

use Zend\Db\RowGateway\AbstractRowGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class User extends AbstractRowGateway
{
    /**
     * Properties of a User
     */
    protected $id;
    protected $username;
    protected $name;
    protected $surname;
    protected $bio;
    protected $location;
    protected $gender;
    
    /**
     * Constructor
     *
     * @param string $primaryKeyColumn
     * @param string|\Zend\Db\Sql\TableIdentifier $table
     * @param Adapter|Sql $adapterOrSql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($primaryKeyColumn, $table, $adapterOrSql = null)
    {
        // setup primary key
        $this->primaryKeyColumn = (array) $primaryKeyColumn;

        // set table
        $this->table = $table;

        // set Sql object
        if ($adapterOrSql instanceof Sql) {
            $this->sql = $adapterOrSql;
        } elseif ($adapterOrSql instanceof Adapter) {
            $this->sql = new Sql($adapterOrSql, $this->table);
        } else {
            throw new Exception\InvalidArgumentException('A valid Sql object was not provided.');
        }

        if ($this->sql->getTable() !== $this->table) {
            throw new Exception\InvalidArgumentException('The Sql object provided does not have a table that matches this row object');
        }

        $this->initialize();
    }
    
    // /**
    //  * This method is called by the TableGateway to populate the object
    //  *
    //  * @param array $data 
    //  * @return void
    //  */
    // public function exchangeArray($data)
    // {
    //     $vars = get_class_vars(get_class($this));
    //     foreach ($data as $k => $v) {
    //         if (array_key_exists($k, $vars)) {
    //             if ($k == 'gender') {
    //                 $v = $this->getGenderString($v);
    //             }
    //             
    //             $this->$k = $v;
    //         }
    //     }
    // }
    
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