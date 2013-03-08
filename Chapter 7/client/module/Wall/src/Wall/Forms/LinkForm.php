<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LinkForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null)
    {
        parent::__construct('link-content');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');
        
        $this->prepareElements();
    }
    
    public function prepareElements()
    {
        $this->add(array(
            'name' => 'url',
            'type'  => 'Zend\Form\Element\Text',
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
            'url' => array(
                'required' => true,
                'validators' => array(
                    array('name' => '\Wall\Validator\Url')
                )
            )
        );
    }
}
