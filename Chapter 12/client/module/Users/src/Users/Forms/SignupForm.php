<?php
namespace Users\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class SignupForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('users-signup');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well form-horizontal');
        
        $this->add(array(
            'name' => 'username',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Username',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'email',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'password',
            'type'  => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'repeat_password',
            'type'  => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Repeat password',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'avatar',
            'type'  => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Avatar',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'name',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'surname',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Surname',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'bio',
            'type'  => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Bio',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'location',
            'type'  => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Location',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        $this->add(array(
            'name' => 'gender',
            'type'  => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'Gender',
                'label_attributes' => array(
                    'class' => 'radio'
                ),
                'value_options' => array(
                    0 => 'Female',
                    1 => 'Male'
                )
            )
        ));
        
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'register',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Register',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}