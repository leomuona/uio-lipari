<?php
/**
* Translate plugin
* author: Leo Muona
*/
class Application_Plugin_TranslatePlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $session = new Zend_Session_Namespace('Default');
        $param = $request->getParam('lang');
        switch ($param) {
            case 'fi':
                $lang = 'fi_FI';
                break;
            /* commented out because no english is not supported yet
            case 'en':
                $lang = 'en_US';
                break;
            */
            default:
                if (isset($session->lang)) {
                    $lang = $session->lang;
                } else {
                    $lang = 'fi_FI';
                }
        }
        $session->lang = $lang;
        $locale = new Zend_Locale($lang);
        $translate = new Zend_Translate('array',
                APPLICATION_PATH . '/languages/finnish.php',
                'fi_FI');
        if ($translate->isAvailable($locale)) {
            $translate->setLocale($locale);
        }
        Zend_Registry::set('translate', $translate);
    }
}
