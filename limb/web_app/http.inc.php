<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    web_app
 */

@define('LIMB_ENABLE_MOD_REWRITE', true); // we assume mod_rewrite in ON by default

if(PHP_SAPI == 'cli')
{
  @define('LIMB_HTTP_GATEWAY_PATH', '/');
  @define('LIMB_HTTP_BASE_PATH', '/');
  @define('LIMB_HTTP_REQUEST_PATH', '/');
  @define('LIMB_HTTP_SHARED_PATH', '/shared');
  @define('LIMB_HTTP_OFFSET_PATH', '');
}
else
{
  $request = lmbToolkit :: instance()->getRequest();

  @define('LIMB_HTTP_REQUEST_PATH', $request->getUri()->toString());

  $offset = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  if($offset && $offset != '.')
    @define('LIMB_HTTP_OFFSET_PATH', $offset . '/');
  else
    @define('LIMB_HTTP_OFFSET_PATH', '');

  if(substr(LIMB_HTTP_OFFSET_PATH, 0, 1) == '/')
  {
    echo('LIMB_HTTP_OFFSET_PATH constant must not have starting slash(' . LIMB_HTTP_OFFSET_PATH . ')!!!');
    exit(1);
  }

  //HTTP_BASE_PATH is defined automatically according to current host and offset settings
  @define('LIMB_HTTP_BASE_PATH', $request->getUri()->toString(array('protocol', 'user', 'password', 'host', 'port')) .
                           '/' . LIMB_HTTP_OFFSET_PATH);

  //check for mod_rewrite
  if(defined('LIMB_ENABLE_MOD_REWRITE') && constant('LIMB_ENABLE_MOD_REWRITE'))
    @define('LIMB_HTTP_GATEWAY_PATH', LIMB_HTTP_BASE_PATH);
  else
    @define('LIMB_HTTP_GATEWAY_PATH', LIMB_HTTP_BASE_PATH . 'index.php/');

  @define('LIMB_HTTP_SHARED_PATH', LIMB_HTTP_BASE_PATH . 'shared/');

  if(substr(LIMB_HTTP_BASE_PATH, -1, 1) != '/')
  {
    echo('LIMB_HTTP_BASE_PATH constant must have trailing slash(' . LIMB_HTTP_BASE_PATH . ')!!!');
    exit(1);
  }

  if(substr(LIMB_HTTP_SHARED_PATH, -1, 1) != '/')
  {
    echo('LIMB_HTTP_SHARED_PATH constant must have trailing slash(' . LIMB_HTTP_SHARED_PATH . ')!!!');
    exit(1);
  }
}
?>