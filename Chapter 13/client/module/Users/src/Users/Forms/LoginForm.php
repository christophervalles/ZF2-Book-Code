<?php
namespace Users\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('users-login');
        
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
            'name' => 'password',
            'type'  => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )
        ));
        
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-primary'
            ),
        ));
    }
}