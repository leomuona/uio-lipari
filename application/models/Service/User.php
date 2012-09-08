<?php
/**
 * A service to handle user management
 * author: Leo Muona
 */
class Application_Model_Service_User
{
    private $_usersdao;
    
    /** constructor */
    public function __construct()
    {
        $this->_usersdao = new Application_Model_Dao_Users();
    }
    
    /**
     * Fetches all enabled users
     * @return array of users
     */
    public function getEnabledUsers()
    {
        return $this->_usersdao->getEnabledUsers();
    }
    
    /**
     * Fetches list of users from usersdao, with offset and size
     * @param int $offset
     * @param int $size
     * @return list of users
     */
    public function getUserList($offset, $size)
    {
        return $this->_usersdao->getPagedEnabledUsers($offset, $size);
    }
    
    /**
     * Fetches number of enabled users from usersdao
     * @return number of enabled users
     */
    public function getUserCount()
    {
        return $this->_usersdao->countEnabledUsers();
    }
    
    /**
    * Handles user create form by checking correct values, updating password if
    * given and creating user. Returns array of errors or null value if success.
    * Sets new user's id to zend registry 'newUserId'
    * @param form - create user form
    * @return null or array of errors
    */
    public function createUserFromForm($form)
    {
        $errorarray = array();
        // password check?
        $pswd1 = $form->getValue('pswd1');
        $pswd2 = $form->getValue('pswd2');
        $ldap = $form->getValue('ldap');
        if (!$ldap && strlen($pswd1) == 0) {
            $errorarray[] = 'error_password_empty';
        }
        if (strcmp($pswd1, $pswd2) != 0) {
            $errorarray[] = 'error_passwords_mismatch';
        }
        
        // username check
        $username = $form->getValue('username');
        $otherUser = $this->_usersdao->getUserByUsername($username);
        if ($otherUser->id > 0) {
            $errorarray[] = 'error_username_taken';
        }
        if (!$ldap && $this->isLdapUsername($username)) {
            $errorarray[] = 'error_ldap_username_taken';
        }
        // email check
        $email = $form->getValue('email');
        $otherUser = $this->_usersdao->getUserByEmail($email);
        if ($otherUser->id > 0) {
            $errorarray[] = 'error_email_taken';
        }
        if (count($errorarray) > 0) {
            return $errorarray;
        }
        $user = new Application_Model_Object_User();
        // set new values and update
        $user->username = $form->getValue('username');
        $user->firstname = $form->getValue('firstname');
        $user->lastname = $form->getValue('lastname');
        $user->email = $form->getValue('email');
        $user->phone = $form->getValue('phone');
        $user->role = $form->getValue('role');
        $user->ldap = $ldap;
        
        $id = $this->_usersdao->createUser($user);
        if (!$user->ldap){
            $this->_usersdao->setPassword($id, $pswd1);
        }
        Zend_Registry::set('newUserId', $id);
        
        return null;
    }
    
    /**
     * Handles user edit form by checking correct values, updating password if
     * given and updating user. Returns array of errors or null value if success.
     * @param form - edit user form
     * @return null or array of errors
     */
    public function updateUserFromForm($form)
    {
        // get user we are editing
        $id = $form->getValue('id');
        $errorarray = array();
        if (!is_numeric($id)) {
            $errorarray[] = 'error_id_not_numeric';
            return $errorarray;
        }
        $user = $this->_usersdao->getUser($id);
        if ($user->id == 0) {
            $errorarray[] = 'error_user_not_found';
            return $errorarray;
        }
        
        // password check?
        $updatePassword = false;
        $pswd1 = $form->getValue('pswd1');
        $pswd2 = $form->getValue('pswd2');
        if (strcmp($pswd1, $pswd2) != 0) {
            $errorarray[] = 'error_passwords_mismatch';
        } else if (strlen($pswd1) > 0) {
            // update user's password
            $updatePassword = true;
        }
        
        // username check
        $username = $form->getValue('username');
        $otherUser = $this->_usersdao->getUserByUsername($username);
        if ($otherUser->id > 0 && $otherUser->id != $user->id) {
            $errorarray[] = 'error_username_taken';
        }
        $ldap = $form->getValue('ldap');
        if (!$ldap && $this->isLdapUsername($username)) {
            $errorarray[] = 'error_ldap_username_taken';
        }
        // email check
        $email = $form->getValue('email');
        $otherUser = $this->_usersdao->getUserByEmail($email);
        if ($otherUser->id > 0 && $otherUser->id != $user->id) {
            $errorarray[] = 'error_email_taken';
        }
        if (count($errorarray) > 0) {
            return $errorarray;
        }
        
        // set new values and update
        $user->username = $form->getValue('username');
        $user->firstname = $form->getValue('firstname');
        $user->lastname = $form->getValue('lastname');
        $user->email = $form->getValue('email');
        $user->phone = $form->getValue('phone');
        $user->role = $form->getValue('role');
        $user->ldap = $ldap;
        $this->_usersdao->updateUser($user);
        if (!$user->ldap && $updatePassword) {
            $this->_usersdao->setPassword($user->id, $pswd1);
        }
        return null;
    }
    
    /**
     * this function checks if Username is in LDAP
     * @return true if username is in LDAP
     */
    public function isLdapUsername()
    {
        $config = new Zend_Config_Ini(
                        APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $options = $config->ldap->server1->toArray();
        $ldap = new Zend_Ldap($options);
        $ldap->bind();
        $result = $ldap->exists(
                        'uid=' . $username . ',' . $config->ldap->server1->baseDn);
        return $result;
    }
    
    /**
     * Fetches user from userdao with id
     * @param int $id
     * @return user
     */
    public function getUser($id)
    {
        return $this->_usersdao->getUser($id);
    }
    
    /**
     * Fetches user via username
     * @param username
     * @return user
     */
    public function getUserByUsername($username)
    {
        return $this->_usersdao->getUserByUsername($username);
    }
    
    /**
     * Fetches user via email
     * @param email
     * @return user
     */
    public function getUserByEmail($email)
    {
        return $this->_usersdao->getUserByEmail($email);
    }
    
    /**
     * Removes user from groups and disables user
     * @param user id
     * @return true if succesful
     */
    public function disableUser($id)
    {
        $groupservice = new Application_Model_Service_Group();
        $groupservice->removeUserFromAllGroups($id);
        return $this->_usersdao->disableUser($id);
    }
}