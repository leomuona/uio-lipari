<?php
/**
 * A service to handle groups
 * author: Leo Muona
 */
class Application_Model_Service_Group
{
    private $_groupsdao;
    private $_groupsusersdao;
    
    /** constructor */
    public function __construct()
    {
        $this->_groupsdao = new Application_Model_Dao_Groups();
        $this->_groupsusersdao = new Application_Model_Dao_GroupsUsers();
    }
    
    /**
     * Fetches list of enabled groups with offset and size
     * @param offset
     * @param size
     * @return array of groups
     */
    public function getGroupList($offset, $size)
    {
        return $this->_groupsdao->getPagedEnabledGroups($offset, $size);
    }
    
    /**
     * Counts enabled groups
     * @return int
     */
    public function getGroupCount()
    {
        return $this->_groupsdao->countEnabledGroups();
    }
    
    /**
     * Fetches group from groupsdao
     * @param id - group id
     * @return group
     */
    public function getGroup($id)
    {
        return $this->_groupsdao->getGroup($id);
    }
    
    /**
     * Handles group create form and saves it to the database if valid.
     * Returns errors or null if success.
     * Sets new group id to zend registry 'newGroupId'
     * @param form - create user form
     * @return null or array of errors
     */
    public function createGroupFromForm($form)
    {
        $errorarray = array();
        $name = $form->getValue('name');
        $otherGroup = $this->_groupsdao->getGroupByName($name);
        if ($otherGroup->id > 0) {
            $errorarray[] = 'error_name_taken';
        }
        if (count($errorarray) > 0) {
            return $errorarray;
        }
        $group = new Application_Model_Object_Group();
        $group->name = $name;
        $group->description = $form->getValue('description');
        
        $id = $this->_groupsdao->createGroup($group);
        Zend_Registry::set('newGroupId', $id);
        
        return null;
    }
    
    /**
     * Updates group from group update form
     * @param $form update group form
     * @return null if success, or array of errors
     */
    public function updateGroupFromForm($form)
    {
        $errorarray = array();
        $id = $form->getValue('id');
        if (!is_numeric($id)) {
            $errorarray[] = 'error_id_not_numeric';
        }
        $group = $this->_groupsdao->getGroup($id);
        if ($group->id == 0) {
            $errorarray[] = 'error_group_not_found';
            return $errorarray;
        }
        $name = $form->getValue('name');
        $otherGroup = $this->_groupsdao->getGroupByName($name);
        if ($otherGroup->id > 0 && $otherGroup->id != $group->id) {
            $errorarray[] = 'error_name_taken';
        }
        if (count($errorarray) > 0) {
            return $errorarray;
        }
        
        // set new values and update
        $group->name = $name;
        $group->description = $form->getValue('description');
        $this->_groupsdao->updateGroup($group);
        return null;
    }
    
    /**
     * Fetches all users that are in group of given id
     * @param group id
     * @return array of users
     */
    public function getUsersOfGroup($groupId)
    {
        return $this->_groupsdao->getUsersByGroupId($groupId);
    }
    
    /**
     * Adds user to group
     * @param group id
     * @param user id
     * @return true if success
     */
    public function addUserToGroup($gid, $uid)
    {
        return $this->_groupsusersdao->addUserToGroup($gid, $uid);
    }
    
    /**
     * Remove user from group
     * @param group id
     * @param user id
     * @return true if success
     */
    public function removeUserFromGroup($gid, $uid)
    {
        return $this->_groupsusersdao->removeUserFromGroup($gid, $uid);
    }
    
    /**
     * Removes user from groups
     * @param unknown_type $uid
     * @return true if success
     */
    public function removeUserFromAllGroups($uid)
    {
        return $this->_groupsusersdao->removeUserFromAllGroups($uid);
    }
}