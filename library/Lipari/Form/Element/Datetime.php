<?php
/**
* Custom form element for datetime
* 
* Based on code from
* http://akrabat.com/zend-framework/a-zend-framwork-compount-form-element-for-dates/
*/
class Lipari_Form_Element_Datetime extends Zend_Form_Element_Xhtml
{
    public $helper = 'formDatetime';

    public function isValid ($value, $context = null)
    {
        if (is_array($value)) {
            $value = $value['year'] . '-' . 
                    $value['month'] . '-' . 
                    $value['day'] . ' ' .
                    $value['hours'] . ':' .
                    $value['minutes'] . ':00';

            if($value == '-- ::00') {
                $value = null;
            }
        }

        return parent::isValid($value, $context);
    }

    public function getValue()
    {
        if(is_array($this->_value)) {
            $value = $this->_value['year'] . '-' .
                    $this->_value['month'] . '-' .
                    $this->_value['day'] . ' ' .
                    $this->_value['hours'] . ':' .
                    $this->_value['minutes'] . ':00';
            if($value == '-- ::00') {
                $value = null;
            }
            $this->setValue($value);
        }
        return parent::getValue();
    }
}
