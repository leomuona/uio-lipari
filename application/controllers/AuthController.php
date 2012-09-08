<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $form = new Application_Form_Login();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                if ($this->_process($form->getValues())) {
                    // authentication done
                    $this->_helper->redirector('index', 'index');
                }
            }
        }
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->_helper->redirector('index','index');
        }
        $this->view->form = $form;
    }

    protected function _process($values)
    {
        $auth = Zend_Auth::getInstance();
        if ($this->_isLdapUser($values['username'])) {
            // LDAP authentication
            $ldapAdapter = $this->_getLdapAuthAdapter();
            $ldapAdapter->setIdentity($values['username']);
            $ldapAdapter->setCredential($values['password']);
            $result = $auth->authenticate($ldapAdapter);
            if ($result->isValid()) {
                return true;
            }
        } else {
            // try database authentication
            $dbAdapter = $this->_getDbAuthAdapter();
            $dbAdapter->setIdentity($values['username']);
            $dbAdapter->setCredential($values['password']);
            $result = $auth->authenticate($dbAdapter);
            if ($result->isValid()) {
                return true;
            }
        }
        return false;
    }

    protected function _isLdapUser($username)
    {
        $usersdao = new Application_Model_Dao_Users();
        $user = $usersdao->getUserByUsername($username);
        if ($user->id > 0 && $user->ldap) {
            return true;
        }
        return false;
    }

    protected function _getLdapAuthAdapter()
    {
        $config = new Zend_Config_Ini(
                APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $options = $config->ldap->toArray();
        $adapter = new Zend_Auth_Adapter_Ldap($options);
        return $adapter;
    }

    protected function _getDbAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment(
                'SHA1(CONCAT(?,password_salt)) AND enabled = 1 AND ldap = 0');
        return $authAdapter;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        // redirect to login index page = login form
        $this->_helper->redirector('index');
    }


}



