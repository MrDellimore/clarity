<?php
/**
 * Created by PhpStorm.
 * User: smergler
 * Date: 1/8/2015
 * Time: 10:37 AM
 */

namespace Authenticate\Controller\Plugin;


use Zend\Form\Annotation\Object;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

/**
 * Class Acl
 * @package Authenticate\Controller\Plugin
 */
class Acl extends AbstractPlugin {
    /**
     * sets up the acl from the roles config file
     * @param MvcEvent $e
     * @return null
     */
    public function initAcl(MvcEvent $e) {
        $acl = new \Zend\Permissions\Acl\Acl();
        $roles = include CONFIG_DIR . "/module.acl.roles.php";
        $allResources = array();
        foreach ($roles as $role => $resources) {

            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl->addRole($role);

            $allResources = array_merge($resources, $allResources);

            //adding resources
            foreach ($resources as $resource) {
                // Edit 4
                if(!$acl->hasResource($resource))
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
            }
            //adding restrictions
            foreach ($allResources as $resource) {
                $acl->allow($role, $resource);
            }
        }

        //setting to view
        $e->getViewModel()->setVariables(array(
            'acl' => $acl,
            /*
             * FIXME: Doing this to get the job done, but this is very very bad practice
             *  there's a way to do this through ZF2, but I haven't quite got it right yet
             */
            'aclPlugin' => $this
        ));


    }

    /**
     * check the acl for the page and redirects the user to a 404 if not allowed
     * @param MvcEvent $e
     * @return null
     */
    public function requireAcl(MvcEvent $e)
    {
        if (!$this->checkAcl($e)) {
            $response = $e->getResponse();
            //location to page or what ever
            $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/404');
            $response->setStatusCode(404);
        }
    }

    /**
     * Checks whether the current logged in user is allowed to visit this page from the MvcEvent
     * @param MvcEvent $e
     * @return bool
     */
    public function checkAcl(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch();
        if (strstr(strtolower($routeMatch->getParam('controller')), 'ajax') != null) {
            return true;
        }
        $route = $routeMatch->getMatchedRouteName();
        $acl = $e->getViewModel()->acl;
        return $this->checkAclByRoute($route, $acl);
    }

    public function checkAclByRoute ($route, $acl){
        //you set your role
        $session = new Container('login');
        $sessionData = $session->sessionDataforUser;
        $userRole = 'guest';
        if ($sessionData) {
            $userRole = $sessionData['role'];
        }
        $allowAll = false;
        if ($acl->hasResource('*') && $acl->isAllowed($userRole, '*')){
            $allowAll = true;
        }
        if (!$allowAll && (!$acl->hasResource($route) || !$acl->isAllowed($userRole, $route))) {
            // var_dump($route);exit();
            return false;
        }
        return true;
    }



}