<?php
/**
 * This controller is supposed to be used with AJAX
 * @author Leo Muona
 */

class GroupUsersController extends Zend_Controller_Action
{

    private $_groupService;
    private $_userService;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->_groupService = new Application_Model_Service_Group();
        $this->_userService = new Application_Model_Service_User();
    }
    
    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
        // action body
    }

    public function groupUsersAction() {
        $gid = isset($_GET['gid']) ? intval($_GET['gid']) : 0;
        if ($gid > 0) {
            $users = $this->_groupService->getUsersOfGroup($gid);
            $this->view->users = $users;
        }
    }

    /**
    * Fetches all users. If $_GET['gid'] is defined, rule those users out
    * from result.
    */
    public function allUsersAction()
    {
        $allusers = $this->_userService->getEnabledUsers();
        if (isset($_GET['gid']) && intval($_GET['gid']) > 0) {
            $gid = intval($_GET['gid']);
            $groupusers = $this->_groupService->getUsersOfGroup($gid);
            $userlist = array();
            foreach ($allusers as $auser) {
                $add = true;
                foreach ($groupusers as $guser) {
                    if ($auser->id == $guser->id) {
                        $add = false;
                        break;
                    }
                }
                if ($add) {
                    $userlist[] = $auser;
                }
            }
            $this->view->users = $userlist;
        } else {
            $this->view->users = $allusers;
        }
    }

    public function addUsersAction()
    {
        $request = $this->getRequest();
        $result = 'FAIL';
        if (isset($_POST['gid']) && isset($_POST['uids'])) {
            $result = 'OK';
            $gid = intval($_POST['gid']);
            foreach ($_POST['uids'] as $uid) {
                $x = $this->_groupService->addUserToGroup($gid, $uid);
                if (!$x) {
                    $result = 'FAIL';
                }
            }
        }
        $this->view->result = $result;
    }

    public function removeUsersAction()
    {
        $request = $this->getRequest();
        $result = 'FAIL';
        if (isset($_POST['gid']) && isset($_POST['uids'])) {
            $result = 'OK';
            $gid = intval($_POST['gid']);
            foreach ($_POST['uids'] as $uid) {
                $x = $this->_groupService->removeUserFromGroup($gid, $uid);
                if (!$x) {
                    $result = 'FAIL';
                }
            }
        }
        $this->view->result = $result;
    }

}

