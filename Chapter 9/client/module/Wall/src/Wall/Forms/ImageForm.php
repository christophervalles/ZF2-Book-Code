<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ImageForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null)
    {
        parent::__construct('image-content');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');
        
        $this->prepareElements();
    }
    
    public function prepareElements()
    {
        $this->add(array(
            'name' => 'image',
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
    
    public function getInputFilterSpecification()
    {
        return array(
            'image' => array(
                'required' => true,
            )
        );
    }
}
