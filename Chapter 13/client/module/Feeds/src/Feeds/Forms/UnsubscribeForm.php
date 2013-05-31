<?php
namespace Feeds\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class UnsubscribeForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('feeds-unsubscribe');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'input-append');
        
        $this->add(array(
            'name' => 'feed_id',
            'type'  => 'Zend\Form\Element\Hidden',
        ));
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'unsubscribe',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Delete subscription',
                'class' => 'btn btn-danger pull-right'
            ),
        ));
    }
}
