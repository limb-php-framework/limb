<?php

$conf = array(
   'main' => array(
      'path' => '/',
      'defaults' => array(
         'controller' => 'main_page',
      )
   )
);

// Common routes, should be included AFTER yours
include_once('limb/web_app/settings/routes.conf.php');
