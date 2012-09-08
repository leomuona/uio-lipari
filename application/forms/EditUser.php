<?php
/**
* User edit form.
* author: Leo Muona
*/
class Application_Form_EditUser extends Zend_Form
{

    public function init()
    {
        $translate = Zend_Registry::get('translate');

        $this->setName("editUser");
        $this->setMethod("post");
        
        /* Form input fields */
        // hidden id
        $this->addElement('hidden', 'id');
        // firstname
        $this->addElement('text', 'firstname', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_firstname'),
        ));
        // lastname
        $this->addElement('text', 'lastname', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_lastname'),
        ));
        // email
        $this->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower', 'StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,255)),
            ),
            'required' => true,
            'label' => $translate->_('label_email'),
        ));
        // phone
        $this->addElement('text', 'phone', array(
            'filters' => array('StringTrim', 'StringToLower', 'StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,32)),
            ),
            'required' => false,
            'label' => $translate->_('label_phone'),
        ));
        // username
        $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower', 'StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_username'),
        ));
        // role
        $roles = array('multiOptions' => array(
            'ROLE_PRINT' => $translate->_('ROLE_PRINT'),
            'ROLE_ORGANIZER' => $translate->_('ROLE_ORGANIZER'),
            'ROLE_ADMIN' => $translate->_('ROLE_ADMIN')
        ));
        $roleElement = new Zend_Form_Element_Select('role', $roles);
        $roleElement->setLabel($translate->_('label_role'));
        $roleElement->setRequired(true);
        $this->addElement($roleElement);
        // ldap
        $this->addElement('checkbox', 'ldap', array(
            'required' => true,
            'label' => $translate->_('label_ldap_user'),
        ));
        // password
        $this->addElement('password', 'pswd1', array(
            'filters' => array('StringTrim', 'StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => false,
            'label' => $translate->_('label_password_new'),
        ));
        // password again
        $this->addElement('password', 'pswd2', array(
            'filters' => array('StringTrim', 'StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => false,
            'label' => $translate->_('label_password_again'),
        ));
        // save button
        $this->addElement('button', 'save', array(
            'type' => 'submit',
            'required' => false,
            'ignore' => true,
            'label' => $translate->_('button_save'),
            'class' => 'green',
        ));
    }

    /**
    * Populates user information into the form.
    */
    public function setUserValues($user) {
        $valueArray = array(
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'username' => $user->username,
            'role' => $user->role,
            'ldap' => $user->ldap
        );
        $this->populate($valueArray);
    }
}

