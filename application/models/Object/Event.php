<?php
/**
* Event object
* author: Leo Muona
*/
class Application_Model_Object_Event
{
    // event states
    public static $STATES = array('WAITING_APPROVAL', 'DRAFT', 'PRINTABLE',
            'ARCHIVED', 'PASSIVE');
    
    public $id = 0;
    public $name;
    public $description;
    public $place;
    public $time;
    public $archiveTime;
    public $maxTickets;
    public $printedTickets;
    private $_state = "WAITING_APPROVAL";
    public $enabled;

    // one-to-many
    public $orgGroup;
    public $ticketTemplate;

    /* constructors */
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }

    private function __construct10($id, $name, $description, $place, $time,
            $archiveTime, $maxTickets, $printedTickets, $state, $enabled)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->place = $place;
        $this->time = $time;
        $this->archiveTime = $archiveTime;
        $this->maxTickets = $maxTickets;
        $this->printedTickets = $printedTickets;
        $this->_state = $state;
        $this->enabled = $enabled;
    }

    /**
    * PHP's magical setter overwrite,
    * called when setting inaccessible properties
    */
    public function __set($name, $value)
    {
        switch ($name) {
        case "state":
            return $this->setState($value);
            break;
        default:
            return null;
        }
    }

    /**
    * PHP's magical getter overwrite,
    * called when getting inaccessible properties
    */
    public function __get($name)
    {
        switch ($name) {
        case "state":
            return $this->getState();
            break;
        default:
            return null;
        }
    }

    public function setState($state)
    {
        if (in_array($state, self::$STATES)) {
            $this->_state = $state;
        } else {
            $this->_state = self::$STATES[0];
        }
    }

    public function getState()
    {
        return $this->_state;
    }
}
