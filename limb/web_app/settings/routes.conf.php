<?php
$conf = array(

'MainPage' =>
  array('path' => '/',
        'defaults' => array('controller' => 'main_page',
                            'action' => 'display')),

'ControllerActionId' =>
  array('path' => '/:controller/:action/:id',
        'defaults' => array('action' => 'display')),

'ControllerAction' =>
  array('path' => '/:controller/:action',
        'defaults' => array('action' => 'display')),

'Controller' =>
  array('path' => '/:controller'),

);
?>
