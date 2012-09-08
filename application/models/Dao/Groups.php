<?php
/**
* Groups DAO
* author: Leo Muona
*/
class Application_Model_Dao_Groups extends Zend_Db_Table_Abstract
{
    protected $_name = "groups";
    protected $_dependentTables = array('Application_Model_Dao_GroupsUsers',
            'Application_Model_Dao_Events');

    public function getGroup($id)
    {
        $group = new Application_Model_Object_Group();
        $row = $this->fetchRow(
            $this->select()->where('id = ?', $id)
        );
        if (!$row) {
            return $group;
        }
        $group->id = $row->id;
        $group->name = $row->name;
        $group->description = $row->description;
        $group->enabled = $row->enabled;
        return $group;
    }

    public function getGroupByName($name)
    {
        $group = new Application_Model_Object_Group();
        $select = $this->select()->where('name = ?', $name);
        $row = $this->fetchRow($select);
        if (!$row) {
            return $group;
        }
        $group->id = $row->id;
        $group->name = $row->name;
        $group->description = $row->description;
        $group->enabled = $row->enabled;
        return $group;
    }
    
    public function getEnabledGroups()
    {
        $groups = array();
        $select = $this->select();
        $select->where('enabled = 1');
        $select->order('name');
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $group = new Application_Model_Object_Group();
            $group->id = $row->id;
            $group->name = $row->name;
            $group->description = $row->description;
            $group->enabled = $row->enabled;
            $groups[] = $group;
        }
        return $groups;
    }

    public function getPagedEnabledGroups($offset, $size)
    {
        $groups = array();
        $select = $this->select();
        $select->where('enabled = 1');
        $select->order('name');
        $select->limit($size, $offset);
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $group = new Application_Model_Object_Group();
            $group->id = $row->id;
            $group->name = $row->name;
            $group->description = $row->description;
            $group->enabled = $row->enabled;
            $groups[] = $group;
        }
        return $groups;
    }

    public function countEnabledGroups()
    {
        $result = 0;
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) as num');
        $select->where('enabled = 1');
        $value = $this->fetchRow($select)->num;
        $result = intval($value);
        return $result;
    }

    public function getUsersByGroupId($id)
    {
        $select = $this->select()->where('id = ?', $id);
        $row = $this->fetchRow($select);
        if (!$row) {
            return null;
        }
        $idRows = $row->findDependentRowset('Application_Model_Dao_GroupsUsers');
        $users = array();
        foreach ($idRows as $idRow) {
            $user = new Application_Model_Object_User();
            $userRow = $idRow->findParentRow('Application_Model_Dao_Users');
            $user->id = $userRow->id;
            $user->username = $userRow->username;
            $user->firstname = $userRow->firstname;
            $user->lastname = $userRow->lastname;
            $user->email = $userRow->email;
            $user->phone = $userRow->phone;
            $user->role = $userRow->role;
            $user->enabled = $userRow->enabled;
            $users[] = $user;
        }
        return $users;
    }

    public function disableGroup($id)
    {
        $data = array('enabled' => false);
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $num = $this->update($data, $where);
        if ($nume > 0) {
            return true;
        }
        return false;
    }

    public function createGroup($group)
    {
        $data = array(
            'name' => $group->name,
            'description' => $group->description,
            'enabled' => true
        );
        $id = $this->insert($data);
        if ($id > 0) {
            return $id;
        }
        return null;
    }

    public function updateGroup($group)
    {
        $data = array(
            'name' => $group->name,
            'description' => $group->description
        );
        $where = $this->getAdapter()->quoteInto('id = ?', $group->id);
        $this->update($data, $where);
    }
}
