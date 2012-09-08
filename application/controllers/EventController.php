<?php

class EventController extends Zend_Controller_Action
{

    private $_eventService = null;

    public function init()
    {
        /* Initialize action controller here */
        $this->_eventService = new Application_Model_Service_Event();
    }

    /**
     * List events
     *
     */
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
        $eventlist = $this->_eventService->getEventList($offset, $size);
        $this->view->events = $eventlist;
        $eventcount = $this->_eventService->getEventCount();
        $pages = ceil($eventcount / $size);
        $this->view->pages = $pages;
        $this->view->page = $page;
    }

    public function createAction()
    {
        $usergroups = $this->_eventService->getGroupsForEventCreation();
        $form = new Application_Form_CreateEvent();
        $form->setGroupSelectValues($usergroups);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $newId = $this->_eventService->createNewEvent($form);
            if ($newId > 0) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                                'redirector');
                $redirector->gotoUrl('/event/show?id=' . $newId)
                        ->redirectAndExit();
            } else {
                $this->view->errors = array('error_create_event_fail');
            }
        }
        $this->view->form = $form;
    }

    public function showAction()
    {
        // action body
    }


}



