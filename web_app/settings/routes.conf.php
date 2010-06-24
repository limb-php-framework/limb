<?php

if (empty($conf))
   $conf = array();

$conf['ControllerActionId'] = array(
   'path' => '/:controller/:action/:id',
);

$conf['ControllerAction'] = array(
   'path' => '/:controller/:action',
);

$conf['Controller'] = array(
   'path' => '/:controller'
);

