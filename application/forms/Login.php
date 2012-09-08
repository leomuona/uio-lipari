<?php
/**
* Login form
* author: Leo Muona
*/
class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $translate = Zend_Registry::get('translate');

        $this->setName("login");
        $this->setMethod("post");
        
        $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_username'),
        ));

        $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_password'),
        ));

        $this->addElement('button', 'login', array(
            'type' => 'submit',
            'required' => false,
            'ignore' => true,
            'label' => $translate->_('button_login'),
            'class' => 'green',
        ));
    }

}

