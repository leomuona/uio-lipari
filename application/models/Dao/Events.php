<?php
/**
* DAO to control users table
* author: Leo Muona
*/
class Application_Model_Dao_Events extends Zend_Db_Table_Abstract
{
    protected $_name = 'events';
    protected $_referenceMap = array(
        'OrgGroup' => array(
            'columns' => array('org_group_id'),
            'refTableClass' => 'Application_Model_Dao_Groups',
            'refColumns' => array('id')
        )
    );

    public function getEvent($id)
    {
        $select = $this->select();
        $select->where('id = ?', $id);
        $row = $this->fetchRow($select);
        $event = new Application_Model_Object_Event();
        if ($row) {
            $event->id = $row->id;
            $event->name = $row->name;
            $event->description = $row->description;
            $event->place = $row->place;
            $event->time = $row->time;
            $event->arvhiceTime = $row->archive_time;
            $event->maxTickets = $row->max_tickets;
            $event->printedTickets = $row->printed_tickets;
            $event->state = $row->state;
            $event->enabled = $row->enabled;
            $event->orgGroup = $row->findParentRow('Application_Model_Dao_Groups');
        } 
        return $event;
    }

    public function getPagedEnabledEvents($offset, $size)
    {
        $events = array();
        $select = $this->select();
        $select->where('enabled = 1');
        $select->order('time');
        $select->limit($size, $offset);
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $event = new Application_Model_Object_Event();
            $event->id = $row->id;
            $event->name = $row->name;
            $event->description = $row->description;
            $event->place = $row->place;
            $event->time = $row->time;
            $event->arvhiceTime = $row->archive_time;
            $event->maxTickets = $row->max_tickets;
            $event->printedTickets = $row->printed_tickets;
            $event->state = $row->state;
            $event->enabled = $row->enabled;
            $event->orgGroup = $row->findParentRow('Application_Model_Dao_Groups');
            $events[] = $event;
        }
        return $events;
    }

    public function getPagedEnabledEventsByGroupIds($offset, $size, $groupIds)
    {
        $events = array();
        $select = $this->select();
        $select->where('enabled = 1 AND org_group_id IN (?)', $groupIds);
        $select->order('time');
        $select->limit($size, $offset);
        $rows = $this->fetchAll($select);
        foreach ($rows as $row) {
            $event = new Application_Model_Object_Event();
            $event->id = $row->id;
            $event->name = $row->name;
            $event->description = $row->description;
            $event->place = $row->place;
            $event->time = $row->time;
            $event->arvhiceTime = $row->archive_time;
            $event->maxTickets = $row->max_tickets;
            $event->printedTickets = $row->printed_tickets;
            $event->state = $row->state;
            $event->enabled = $row->enabled;
            $event->orgGroup = $row->findParentRow('Application_Model_Dao_Groups');
            $events[] = $event;
        }
        return $events;

    }

    public function countEnabledEvents()
    {
        $result = 0;
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) as num');
        $select->where('enabled = 1');
        $value = $this->fetchRow($select)->num;
        $result = intval($value);
        return $result;
    }

    public function countEnabledEventsByGroupIds($groupIds)
    {
        $result = 0;
        $select = $this->select();
        $select->from($this->_name, 'COUNT(*) as num');
        $select->where('enabled = 1 AND org_group_id IN (?)', $groupIds);
        $value = $this->fetchRow($select)->num;
        $result = intval($value);
        return $result;
    }
    
    public function createEvent($event)
    {
        $data = array(
                'name' => $event->name,
                'description' => $event->description,
                'place' => $event->place,
                'time' => $event->time,
                'archive_time' => $event->archiveTime,
                'max_tickets' => $event->maxTickets,
                'enabled' => $event->enabled,
                'printed_tickets' => $event->printedTickets,
                'state' => $event->state,
                'org_group_id' => $event->orgGroup,
                'ticket_template_id' => 0
        );
        $id = $this->insert($data);
        if ($id > 0) {
            return $id;
        }
        return null;
    }
    
}
