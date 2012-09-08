<?php
/**
* Table that connects many-to-many relationship between Users and Groups
* author: Leo Muona
*/
class Application_Model_Dao_GroupsUsers extends Zend_Db_Table_Abstract
{
    protected $_name = "groups_users";
    protected $_referenceMap = array(
        'Group' => array(
            'columns' => array('group_id'),
            'refTableClass' => 'Application_Model_Dao_Groups',
            'refColumns' => array('id')
        ),
        'User' => array(
            'columns' => array('user_id'),
            'refTableClass' => 'Application_Model_Dao_Users',
            'refColumns' => array('id')
        )
    );

    public function addUserToGroup($gid, $uid) {
        $data = array(
            'group_id' => $gid,
            'user_id' => $uid
        );
        $id = $this->insert($data);
        if ($id > 0) {
            return true;
        }
        return false;
    }

    public function removeUserFromGroup($gid, $uid) {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('group_id = ?', $gid);
        $where[] = $this->getAdapter()->quoteInto('user_id = ?', $uid);
        $result = $this->delete($where);
        if ($result > 0) {
            return true;
        }
        return false;
    }
    
    public function removeUserFromAllGroups($uid)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('user_id = ?', $uid);
        $result = $this->delete($where);
        if ($result > 0) {
            return true;
        }
        return false;
    }
}
