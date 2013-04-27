<?php
namespace News\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class SubscribeForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('news-subscribe');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');
        
        $this->add(array(
            'name' => 'url',
            'type'  => 'Zend\Form\Element\Url',
            'attributes' => array(
                'class' => 'span11',
                'placeholder' => 'Insert the feed RSS'
            ),
        ));
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'subscribe',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Subscribe',
                'class' => 'btn'
            ),
        ));
    }
}
