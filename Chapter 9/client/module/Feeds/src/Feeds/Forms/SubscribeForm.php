<?php
namespace Feeds\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class SubscribeForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('feeds-subscribe');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'input-append');
        
        $this->add(array(
            'name' => 'url',
            'type'  => 'Zend\Form\Element\Url',
            'attributes' => array(
                'class' => 'input-medium',
                'placeholder' => 'http://feeds.feedbu...'
            ),
        ));
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'subscribe',
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}
