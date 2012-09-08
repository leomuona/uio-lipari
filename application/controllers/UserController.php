<?php

class UserController extends Zend_Controller_Action
{

    private $_userService;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->_userService = new Application_Model_Service_User();
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
        $users = $this->_userService->getUserList($offset, $size);
        $this->view->users = $users;
        $usercount = $this->_userService->getUserCount();
        $pages = ceil($usercount / $size);
        $this->view->pages = $pages;
        $this->view->page = $page;
    }

    public function createAction()
    {
        $form = new Application_Form_CreateUser();
        $request = $this->getRequest();

        if ($request->isPost() && $form->isValid($request->getPost())) {
            $errors = $this->_userService->createUserFromForm($form);
            if ($errors != null) {
                $this->view->errors = $errors;
            } else {
                $id = Zend_Registry::get('newUserId');
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/user/show?id=' . intval($id))
                        ->redirectAndExit();
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        $form = new Application_Form_EditUser();
        $request = $this->getRequest();

        if ($request->isPost() && $form->isValid($request->getPost())) {
            // do username check and password checks
            $errors = $this->_userService->updateUserFromForm($form);
            if ($errors != null) {
                $user = $this->_userService->getUser(intval($_POST['id']));
                $form->setUserValues($user);
                $this->view->errors = $errors;
            } else {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/user/show?id=' . intval($_POST['id']))
                        ->redirectAndExit();
            }
        } else {
            $id = $_GET['id'];
            if (!is_numeric($id)) {
                // invalid user id
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/user/')->redirectAndExit();
            }
            $user = $this->_userService->getUser($id);
            if ($user->id == 0) {
                // no user found
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'redirector');
                $redirector->gotoUrl('/user/')->redirectAndExit();
            }
            $form->setUserValues($user);
        }
        $this->view->form = $form;
    }

    public function showAction()
    {
        // redirect if not valid id.
        $redirect = true;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id > 0) {
                $user = $this->_userService->getUser($id);
                if ($user != null && $user->id != 0) {
                    // valid user
                    $redirect = false;
                    $this->view->user = $user;
                }
            }
        }

        if ($redirect) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                'redirector');
            $redirector->gotoUrl('/user/')->redirectAndExit();   
        }
    }
    
    public function deleteAction()
    {
        $result = false;
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
            if ($id > 0) {
                $this->view->user = $this->_userService->getUser($id);
                $result = $this->_userService->disableUser($id);
            }
        }
        if ($result) {
            $this->view->message = 'user_delete_succesful';
        } else {
            $this->view->message = 'user_delete_failure';
        }
    }
}

