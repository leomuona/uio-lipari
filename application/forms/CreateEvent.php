<?php

class Application_Form_CreateEvent extends Zend_Form
{

    public function init()
    {
        $translate = Zend_Registry::get('translate');

        $this->setName("createEvent");
        $this->setMethod("post");
        // load custom form elements from library/Lipari/Form/
        $this->addPrefixPath('Lipari_Form', 'Lipari/Form/');
        /* Form imput fields */
        // organizing group
        $grps = array('multiOptions' => array());
        $groupElement = new Zend_Form_Element_Select('org_group', $grps);
        $groupElement->setLabel($translate->_('label_organizing_group'));
        $groupElement->setRequired(true);
        $this->addElement($groupElement);
        // name
        $this->addElement('text', 'name', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,32)),
            ),
            'required' => true,
            'label' => $translate->_('label_name'),
        ));
        // time
        $this->addElement('datetime', 'time', array(
            'required' => true,
            'label' => $translate->_('label_time'),
            'value' => 'now'
        ));
        // place
        $this->addElement('text', 'place', array(
            'filters' => array('StripTags'),
            'validators' => array(
                array('StringLength', false, array(0,255)),
            ),
            'required' => false,
            'label' => $translate->_('label_place'),
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
        // max tickets
        $this->addElement('text', 'max_tickets', array(
            'filters' => array('StripTags', 'StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0,11),
                'Int')
            ),
            'required' => false,
            'label' => $translate->_('label_max_tickets')
        ));
        // archive time
        $this->addElement('datetime', 'archive_time', array(
            'required' => false,
            'label' => $translate->_('label_archive_time'),
            'value' => 'now'
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

    public function setGroupSelectValues($groups)
    {
        $options = array();
        foreach ($groups as $group) {
            $options[$group->id] = $group->name;
        }
        $groupElement = $this->getElement('org_group');
        $groupElement->setMultiOptions($options);
    }

}

