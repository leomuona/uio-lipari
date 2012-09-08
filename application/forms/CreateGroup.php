<?php
/*
* Form to create a new group
* author: Leo Muona
*/
class Application_Form_CreateGroup extends Zend_Form
{

    public function init()
    {
        $translate = Zend_Registry::get('translate');

        $this->setName("createGroup");
        $this->setMethod("post");

        /* Form input fields*/
        // name
        $this->addElement('text', 'name', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,64)),
            ),
            'required' => true,
            'label' => $translate->_('label_name'),
        ));
        // description
        $this->addElement('textarea', 'description', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,255)),
            ),
            'required' => false,
            'label' => $translate->_('label_description'),
            'rows' => '10',
            'cols' => '40',
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
    * Sets group values into form
    */
    public function setGroupValues($group) {
        $valueArray = array(
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description
        );
        $this->populate($valueArray);
    }
}

