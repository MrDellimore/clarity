<?php

return array(


    'router' => array(
        'routes' => array(

            'kanban' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/helpdesk/kanban',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\Kanban',
                        'action'     => 'index',
                    )
                ),
            ),

            'tickets' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/helpdesk/tickets',
                    'defaults' => array(
                        'controller' => 'Helpdesk\Controller\Ticket',
                        'action'     => 'index',
                    )
                ),
            ),

        ),
    ),

    'controllers' => array('invokables' => array('Helpdesk\Controller\Kanban' => 'Helpdesk\Controller\KanbanController',
                                                'Helpdesk\Controller\Ticket' => 'Helpdesk\Controller\TicketController',)),

    'view_manager' => array('template_path_stack' => array(__DIR__ . '/../view')),

);
