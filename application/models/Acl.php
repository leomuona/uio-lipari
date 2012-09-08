<?php
/**
* Customized Zend_Acl class.
* author: Leo Muona
*/
class Application_Model_Acl extends Zend_Acl
{
    protected static $_instance = null;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    protected function _initialize()
    {
        // create roles
        $this->addRole(new Zend_Acl_Role('ROLE_ANONYMOUS'));
        $this->addRole(new Zend_Acl_Role('ROLE_PRINT'), 'ROLE_ANONYMOUS');
        $this->addRole(new Zend_Acl_Role('ROLE_ORGANIZER'), 'ROLE_PRINT');
        $this->addRole(new Zend_Acl_Role('ROLE_ADMIN'));

        // add all resources
        $this->add(new Zend_Acl_Resource('IndexController'));
        $this->add(new Zend_Acl_Resource('ErrorController'));
        $this->add(new Zend_Acl_Resource('AuthController'));
        $this->add(new Zend_Acl_Resource('UserController'));
        $this->add(new Zend_Acl_Resource('GroupController'));
        $this->add(new Zend_Acl_Resource('GroupUsersController'));
        $this->add(new Zend_Acl_Resource('EventController'));
        $this->add(new Zend_Acl_Resource('TicketPrintController'));
        $this->add(new Zend_Acl_Resource('ProfileController'));
        $this->add(new Zend_Acl_Resource('ReportController'));

        // allow roles
        // ROLE_ANONYMOUS
        $this->deny('ROLE_ANONYMOUS');
        $this->allow('ROLE_ANONYMOUS',
            array('ErrorController', 'AuthController'));
        // ROLE_PRINT
        $this->allow('ROLE_PRINT', array(
            'IndexController',
            'TicketPrintController',
            'ProfileController'));
        // ROLE_ORGANIZER
        $this->allow('ROLE_ORGANIZER', array(
            'EventController'));
        // ROLE_ADMIN
        $this->allow('ROLE_ADMIN');
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
            self::$_instance->_initialize();
        }
        return self::$_instance;
    }
}
