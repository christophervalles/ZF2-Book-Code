<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class ImageForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('image-content');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');
        
        $this->add(array(
            'name' => 'image',
            'required' => true,
            'type'  => 'Zend\Form\Element\File',
            'attributes' => array(
                'class' => 'span11',
            ),
        ));
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'class' => 'btn'
            ),
        ));
    }
}
