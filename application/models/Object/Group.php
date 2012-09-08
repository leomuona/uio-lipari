<?php
/**
* Group object
* author: Leo Muona
*/
class Application_Model_Object_Group
{
    public $id = 0;
    public $name;
    public $description;
    public $enabled;
    
    public $users = array();

    /** constructor */
    public function __construct()
    {
        $a = func_get_args();
        $i = func_num_args();
        if (method_exists($this, $f='__construct'.$i)) {
            call_user_func_array(array($this,$f),$a);
        }
    }

    public function __construct3($id, $name, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
}
