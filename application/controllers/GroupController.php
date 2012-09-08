<?php
/**
 * Group controller
 * author: Leo Muona
 *
 */
class GroupController extends Zend_Controller_Action
{
    private $_groupService;

    public function init()
    {
        /* Initialize action controller here */
        $this->_groupService = new Application_Model_Service_Group();
    }

    public function indexAction()
    {
        $offset = 0;
        $size = 40;
        $page = 1;
        if (isset($_GET['page'])) {
            $page = intval($_GET['page']);
            if ($page <= 0) {
                $page = 1;
            }
            $offset = ($page - 1) * $size;
        }
        $groups = $this->_groupService->getGroupList($offset, $size);
        $this->view->groups = $groups;
        $groupcount = $this->_groupService->getGroupCount();
        $pages = ceil($groupcount / $size);
        $this->view->pages = $pages;
        $this->view->page = $page;
    }

    public function createAction()
    {
        $form = new Application_Form_CreateGroup();
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $errors = $this->_groupService->createGroupFromForm($form);
            if ($errors != null) {
                $this->view->errors = $errors;
            } else {
                $id = Zend_Registry::get('newGroupId');
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'redirector');
                $redirector->gotoUrl('/group/show?id=' . intval($id))
                        ->redirectAndExit();
            }
        }
        $this->view->form = $form;
    }

    public function showAction()
    {
        $redirect = true;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id > 0) {
                $group = $this->_groupService->getGroup($id);
                if ($group != null && $group->id != 0) {
                    $redirect = false;
                    $this->view->group = $group;
                    $users = $this->_groupService->getUsersOfGroup($group->id);
                    if ($users != null) {
                        $this->view->users = $users;
                    }
                }
            }
        }
        if ($redirect) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'redirector');
            $redirector->gotoUrl('/group/')->redirectAndExit();
        }    
    }

    public function editAction()
    {
        $form = new Application_Form_EditGroup();
        $request = $this->getRequest();

        if ($request->isPost() && $form->isValid($request->getPost())) {
            // do name check
            $errors = $this->_groupService->updateGroupFromForm($form);
            if ($errors != null) {
                $group = $this->_groupService->getGroup(intval($_POST['id']));
                $form->setGroupValues($group);
                $this->view->errors = $erros;
            } else {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/group/show?id=' . intval($_POST['id']))
                        ->redirectAndExit();
            }
        } else {
            $id = $_GET['id'];
            if (!is_numeric($id)) {
                // invalid group id
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/group/')->redirectAndExit();
            }
            $group = $this->_groupService->getGroup($id);
            if ($group->id == 0) {
                // no group found
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/group/')->redirectAndExit();
            }
            $form->setGroupValues($group);
        }
        $this->view->form = $form;
    }

}
