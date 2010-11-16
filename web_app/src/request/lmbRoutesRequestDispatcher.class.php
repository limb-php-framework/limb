<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');

/**
 * class lmbRoutesRequestDispatcher.
 *
 * @package web_app
 */
class lmbRoutesRequestDispatcher implements lmbRequestDispatcher
{
  function dispatch($request)
  {
    $routes = lmbToolkit :: instance()->getRoutes();

    $uri = $request->getUri();
    $uri->normalizePath();

    if(method_exists($request, 'getRequestMethod'))
      $result = $routes->dispatch($uri, $request->getRequestMethod());
    else
      $result = $routes->dispatch($uri);

    if($action = $request->get('action'))
      $result['action'] = $action;
    if($controller = $request->get('controller'))
      $result['controller'] = $controller;
    return $result;
  }
}


