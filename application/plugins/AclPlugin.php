<?php
/**
* Plugin to autocheck the ACL
* author: Leo Muona
*/
class Application_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $acl = Application_Model_Acl::getInstance();
        $role = 'ROLE_ANONYMOUS';
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            // identity = username
            $userService = new Application_Model_Service_User();
            $user = $userService->getUserByUsername($auth->getIdentity());
            $role = $user->role;
            Zend_Registry::set('user', $user);
        }

        $frontController = Zend_Controller_Front::getInstance();
        $dispatcher = $frontController->getDispatcher();
        $controller = $dispatcher->getControllerClass($request);
        $action = $dispatcher->getActionMethod($request);

        $status = $acl->isAllowed($role, $controller, $action);
        if (!$status) {
            // no permission -> redirect to login form
            $red = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'redirector');
            $red->gotoUrl('/auth/index')->redirectAndExit();
        }
        return true;
    }
}
