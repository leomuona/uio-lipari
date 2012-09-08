<?php
/**
* The infamous Bootstrap class
* author: Leo Muona
*/
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_AclPlugin());
        $front->registerPlugin(new Application_Plugin_TranslatePlugin());
    }

}

