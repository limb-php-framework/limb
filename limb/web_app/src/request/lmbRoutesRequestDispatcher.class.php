<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRoutesRequestDispatcher.class.php 5286 2007-03-20 08:31:30Z serega $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');

class lmbRoutesRequestDispatcher implements lmbRequestDispatcher
{
  protected $path_offset;
  protected $base_path;

  function __construct($path_offset = null, $base_path = null)
  {
    if(is_null($path_offset) && defined('LIMB_HTTP_OFFSET_PATH'))
      $this->path_offset = LIMB_HTTP_OFFSET_PATH;
    else
      $this->path_offset = $path_offset;

    if(is_null($base_path) && defined('LIMB_HTTP_BASE_PATH'))
      $this->base_path = LIMB_HTTP_BASE_PATH;
    else
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

?>
