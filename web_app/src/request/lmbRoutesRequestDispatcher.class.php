<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');

/**
 * class lmbRoutesRequestDispatcher.
 *
 * @package web_app
 * @version $Id: lmbRoutesRequestDispatcher.class.php 7676 2009-03-03 22:37:28Z korchasa $
 */
class lmbRoutesRequestDispatcher implements lmbRequestDispatcher
{
  protected $path_offset;
  protected $base_path;

  function __construct($path_offset = null, $base_path = null)
  {
  	$this->path_offset = lmb_env_get('LIMB_HTTP_OFFSET_PATH');  	
    if(!is_null($path_offset))
      $this->path_offset = $path_offset;

    $this->base_path = lmb_env_get('LIMB_HTTP_BASE_PATH');    
    if(!is_null($base_path))
      $this->base_path = $base_path;
  }

  function dispatch($request)
  {
    $routes = lmbToolkit :: instance()->getRoutes();

    $uri = $request->getUri();
    $uri->normalizePath();

    $level = $this->_getHttpBasePathOffsetLevel($uri);

    $result = $routes->dispatch($uri->getPathFromLevel($level));

    if($action = $request->get('action'))
      $result['action'] = $action;
    if($controller = $request->get('controller'))
      $result['controller'] = $controller;
    return $result;
  }

  protected function _getHttpBasePathOffsetLevel($uri)
  {
    if(!$this->path_offset)
      return 0;

    $base_path_uri = new lmbUri(rtrim($this->base_path, '/'));
    $base_path_uri->normalizePath();

    $level = 1;
    while(($uri->getPathElement($level) == $base_path_uri->getPathElement($level)) &&
          ($level < $base_path_uri->countPath()))
    {
      $level++;
    }

    return $level;
  }
}


