<?php
/**
* Users DAO
* author: Leo Muona
*/
class Application_Model_Dao_Users extends Zend_Db_Table_Abstract
{
    protected $_name = "users";
    protected $_dependentTables = array('Application_Model_Dao_GroupsUsers');

    public function getUser($id)
    {
        $user = new Application_Model_Object_User();
        $row = $this->fetchRow(
            $this->select()->where('id = ?', $id)
        );
        if (!$row) {
            return $user;
        }
        $user->id = $row->id;
        $user->username = $row->username;
        $user->firstname = $row->firstname;
        $user->lastname = $row->lastname;
        $user->email = $row->email;
        $user->phone = $row->phone;
        $user->role = $row->role;
        $user->ldap = $row->ldap;
        $user->enabled = $row->enabled;
        return $user;
    }

    public function getEnabledUsers()
    {
        $users = array();
        $select = $this->select()->where('enabled = 1')->order('username');
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $user = new Application_Model_Object_User();
            $user->id = $row->id;
            $user->username = $row->username;
            $user->firstname = $row->firstname;
            $user->lastname = $row->lastname;
            $user->email = $row->email;
            $user->phone = $row->phone;
            $user->role = $row->role;
            $user->ldap = $row->ldap;
            $user->enabled = $row->enabled;
            $users[] = $user;
        }
        return $users;
    }

    /**
    * Gets enabled users with certain offset and size
    * Params: offset is the amount of rows to skip, size is the rows wanted.
    */
    public function getPagedEnabledUsers($offset, $size)
    {
        $users = array();
        $select = $this->select();
        $select->where('enabled = 1');
        $select->order('username');
        $select->limit($size, $offset);
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $user = new Application_Model_Object_User();
            $user->id = $row->id;
            $user->username = $row->username;
            $user->firstname = $row->firstname;
            $user->lastname = $row->lastname;
            $user->email = $row->email;
            $user->phone = $row->phone;
            $user->role = $row->role;
            $user->ldap = $row->ldap;
            $user->enabled = $row->enabled;
            $users[] = $user;
        }
        return $users;
    }

    public function countEnabledUsers()
    {
        $result = 0;
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) as num');
        $select->where('enabled = 1');
        $value = $this->fetchRow($select)->num;
        $result = intval($value);
        return $result;
    }

    public function getUserByUsername($username)
    {
        $user = new Application_Model_Object_User();
        $select = $this->select()->where('username = ?', $username);
        $row = $this->fetchRow($select);
        if (!$row) {
            return $user;
        }
        $user->id = $row->id;
        $user->username = $row->username;
        $user->firstname = $row->firstname;
        $user->lastname = $row->lastname;
        $user->email = $row->email;
        $user->phone = $row->phone;
        $user->role = $row->role;
        $user->ldap = $row->ldap;
        $user->enabled = $row->enabled;
        return $user;
    }
    
    public function getUserByEmail($email)
    {
        $user = new Application_Model_Object_User();
        $select = $this->select()->where('email = ?', $email);
        $row = $this->fetchRow($select);
        if (!$row) {
            return $user;
        }
        $user->id = $row->id;
        $user->username = $row->username;
        $user->firstname = $row->firstname;
        $user->lastname = $row->lastname;
        $user->email = $row->email;
        $user->phone = $row->phone;
        $user->role = $row->role;
        $user->ldap = $row->ldap;
        $user->enabled = $row->enabled;
        return $user;
    }

    public function updateUser($user)
    {
        $data = array(
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'ldap' => $user->ldap
        );
        $where = $this->getAdapter()->quoteInto('id = ?', $user->id);
        $this->update($data, $where);
    }

    public function createUser($user)
    {
        $data = array(
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'ldap' => $user->ldap,
            'password_salt' => $this->_createPasswordSalt(10)
        );
        $id = $this->insert($data);
        if ($id > 0) {
            return $id;
        }
        return null;
    }

    public function disableUser($id)
    {
        $data = array('enabled' => false);
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $num = $this->update($data, $where);
        if ($num > 0) {
            return true;
        }
        return false;
    }

    public function setPassword($userid, $password)
    {
        $id = intval($userid);
        $select = $this->select();
        $select->from($this->_name, 'password_salt as salt')->where('id = ?', $id);
        $salt = $this->fetchRow($select)->salt;
        $shavalue = sha1($password . $salt);
        $data = array('password' => $shavalue);
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }

    public function getGroupsByUserId($id)
    {
        $grouplist = array();
        $select = $this->select()->where('id = ?', $id);
        $row = $this->fetchRow($select);
        if (!$row) {
            return $grouplist;
        }
        $groupRows = $row->findManyToManyRowset('Application_Model_Dao_Groups',
                'Application_Model_Dao_GroupsUsers');
        foreach ($groupRows as $grow) {
            $group = new Application_Model_Object_Group();
            $group->id = $grow->id;
            $group->name = $grow->name;
            $group->enabled = $row->enabled;
            $grouplist[] = $group;
        }
        return $grouplist;
    }

    private function _createPasswordSalt($size)
    {
        $random_string = "";
        $valid_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $vc_max_index = strlen($valid_chars) - 1;
        for ($i = 0; $i < $size; $i++) {
            $random_string .= $valid_chars[mt_rand(0, $vc_max_index)];
        }
        return $random_string;
    }
}
