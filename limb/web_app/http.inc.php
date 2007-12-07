<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 * @version $Id$
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

  if(!defined('LIMB_HTTP_REQUEST_PATH'))
    define('LIMB_HTTP_REQUEST_PATH', $request->getUri()->toString());

  if(!defined('LIMB_HTTP_OFFSET_PATH'))
  {
    $offset = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if($offset && $offset != '.')
      define('LIMB_HTTP_OFFSET_PATH', $offset . '/');
    else
      define('LIMB_HTTP_OFFSET_PATH', '');
  }

  if(substr(LIMB_HTTP_OFFSET_PATH, 0, 1) == '/')
    throw new lmbException('LIMB_HTTP_OFFSET_PATH constant must not have starting slash(' . LIMB_HTTP_OFFSET_PATH . ')!!!');

  //HTTP_BASE_PATH is defined automatically according to current host and offset settings
  if(!defined('LIMB_HTTP_BASE_PATH'))
  {
    define('LIMB_HTTP_BASE_PATH', $request->getUri()->toString(array('protocol', 'user', 'password', 'host', 'port')) .
                           '/' . LIMB_HTTP_OFFSET_PATH);
  }

  if(!defined('LIMB_HTTP_GATEWAY_PATH'))
  {
    if(defined('LIMB_ENABLE_MOD_REWRITE') && constant('LIMB_ENABLE_MOD_REWRITE'))
      define('LIMB_HTTP_GATEWAY_PATH', LIMB_HTTP_BASE_PATH);
    else
      define('LIMB_HTTP_GATEWAY_PATH', LIMB_HTTP_BASE_PATH . 'index.php/');
  }

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

