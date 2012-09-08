<?php
/**
* User class object.
* Author: Leo Muona
*/
class Application_Model_Object_User
{

    public $id = 0;
    public $username;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    private $_role = "ROLE_ANONYMOUS";
    public $ldap;
    public $enabled;
    
    public $groups = array();

    /** constructors */
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);    
        }
    }

    private function __construct10($id, $username, $firstname, $lastname,
            $email, $phone, $role, $ldap, $enabled, $groups)
    {
        $this->id = $id;
        $this->username = $username;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->phone = $phone;
        $this->_role = $role;
        $this->ldap = $ldap;
        $this->enabled = $enabled;
        $this->groups = $groups;
    }

    /**
    * PHP's magical setter overwrite, 
    * called when setting inaccessible properties
    */
    public function __set($name, $value)
    {
        switch ($name) {
        case "role":
            return $this->setRole($value);
            break;
        default:
            return null;
        }
    }

    /**
    * PHP's magical getter overwrite,
    * called when getting inaccessible properties
    */
    public function __get($name)
    {
        switch ($name) {
        case "role":
            return $this->getRole();
            break;
        default:
            return null;
        }
    }

    /**
    * User->_role setter and getter.
    * Only allowed values are ROLE_PRINT, ROLE_ORGANIZER and ROLE_ADMIN
    */
    public function setRole($role)
    {
        switch ($role) {
        case "ROLE_PRINT":
            $this->_role = $role;
            break;
        case "ROLE_ORGANIZER":
            $this->_role = $role;
        case "ROLE_ADMIN":
            $this->_role = $role;
            break;
        default:
            $this->_role = "ROLE_ANONYMOUS";
        }
        return $this->_role;
    }
    public function getRole()
    {
        return $this->_role;
    }

    /** Returns "firstName lastName" */
    public function getFullname()
    {
        return $this->firstname . " " . $this->lastname;
    }
}
