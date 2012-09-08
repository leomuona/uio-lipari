<?php
/**
* A service to handle event management
* author: Leo Muona
*/
class Application_Model_Service_Event
{
    private $_eventsdao;
    private $_usersdao;
    private $_groupsdao;
    
    public function __construct()
    {
        $this->_eventsdao = new Application_Model_Dao_Events();
        $this->_usersdao = new Application_Model_Dao_Users();
        $this->_groupsdao = new Application_Model_Dao_Groups();
    }
    
    public function getEventList($offset, $size)
    {
        $eventlist = array();
        $user = Zend_Registry::get('user');
        if (!$user) {
            return $eventlist;
        }

        if (strcmp($user->role, 'ROLE_ADMIN') == 0) {
            $eventlist = $this->_eventsdao->getPagedEnabledEvents($offset, $size);
        } else {
            $groups = $this->_usersdao->getGroupsByUserId($user->id);
            $groupids = array();
            foreach ($groups as $group) {
                $groupids[] = $group->id;
            }
            if (count($groupids) > 0) {
                $eventlist = $this->_eventsdao
                    ->getPagedEnabledEventsByGroupIds($offset, $size, $groupids);
            }
        }
        return $eventlist;
    }

    public function getEventCount()
    {
        $result = 0;
        $user = Zend_Registry::get('user');
        if (!$user) {
            return $result;
        }
        if (strcmp($user->role, 'ROLE_ADMIN') == 0) {
            $result = $this->_eventsdao->countEnabledEvents();
        } else {
            $groups = $this->_usersdao->getGroupsByUserId($user->id);
            $groupids = array();
            foreach ($groups as $group) {
                $groupids[] = $group->id;
            }
            if (count($groupids) > 0) {
                $result = $this->_eventsdao->countEnabledEventsByGroupIds($groupids);
            }
        }
        return $result;
    }

    public function getGroupsForEventCreation()
    {
        $grouplist = array();
        $user = Zend_Registry::get('user');
        if (strcmp($user->role, 'ROLE_ADMIN') == 0) {
            $grouplist = $this->_groupsdao->getEnabledGroups();
        } else {
            $grouplist = $this->_usersdao->getGroupsByUserId($user->id);
        }
        return $grouplist;
    }

    /**
     * Creates new event. returns event id or zero if fails.
     * @param $eventform
     * @return event id as integer. Zero if fails.
     */
    public function createNewEvent($eventform)
    {
        $event = new Application_Model_Object_Event();
        $event->name = $eventform->getValue('name');
        $event->description = $eventform->getValue('description');
        $event->place = $eventform->getValue('place');
        $event->time = $eventform->getValue('time');
        $event->archiveTime = $eventform->getValue('archive_time');
        $event->maxTickets = $eventform->getValue('max_tickets');
        $event->enabled = true;
        $event->printedTickets = 0;
        $event->orgGroup = $eventform->getValue('org_group');
        
        // validate datetimes
        $eventTime = new DateTime($event->time);
        $arcTime = new DateTime($event->archiveTime);
        if ($eventTime > $arcTime) {
            // event time is after archive time. return zero to indicate error.
            return 0;
        }
                
        $id = $this->_eventsdao->createEvent($event);
        if ($id == null || $id <= 0) {
            return 0;
        }
        return $id;
    }
    
    public function changeEventState($eventId, $state)
    {
        $event = $this->_eventsdao->getEvent($eventId);
        // TODO
    }
}
