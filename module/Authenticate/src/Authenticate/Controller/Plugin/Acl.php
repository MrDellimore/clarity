<?php
/**
 * Created by PhpStorm.
 * User: smergler
 * Date: 1/8/2015
 * Time: 10:37 AM
 */

namespace Authenticate\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Acl extends AbstractPlugin {
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
        //testing
        //var_dump($acl->isAllowed('admin','home'));
        //true

        //setting to view
        $e -> getViewModel() -> acl = $acl;

    }

    public function checkAcl(MvcEvent $e) {
        $routeMatch = $e->getRouteMatch();
        $route = $routeMatch->getMatchedRouteName();
        //you set your role
        $session = new Container('login');
        $sessionData = $session->sessionDataforUser;
        $userRole = 'guest';
        if ($sessionData) {
            $userRole = $sessionData['role'];
        }
        $acl = $e->getViewModel()->acl;
        $allowAll = false;
        if ($acl->hasResource('*') && $acl->isAllowed($userRole, '*')){
            $allowAll = true;
        }
        if (!$allowAll && (!$acl->hasResource($route) || !$acl->isAllowed($userRole, $route))) {
            $response = $e -> getResponse();
            //location to page or what ever
            $response -> getHeaders() -> addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/404');
            $response -> setStatusCode(404);

        }
    }
}