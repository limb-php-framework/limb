<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 * @version $Id$
 */
lmb_env_setor('LIMB_ENABLE_MOD_REWRITE', true); // we assume mod_rewrite in ON by default

if(PHP_SAPI == 'cli')
{
  lmb_env_setor('LIMB_HTTP_GATEWAY_PATH', '/');
  lmb_env_setor('LIMB_HTTP_BASE_PATH', '/');
  lmb_env_setor('LIMB_HTTP_REQUEST_PATH', '/');
  lmb_env_setor('LIMB_HTTP_SHARED_PATH', '/shared');
  lmb_env_setor('LIMB_HTTP_OFFSET_PATH', '');
}
else
{
  $request = lmbToolkit :: instance()->getRequest();

  lmb_env_setor('LIMB_HTTP_REQUEST_PATH', $request->getUri()->toString());

  if(!lmb_env_has('LIMB_HTTP_OFFSET_PATH'))
  {
    $offset = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if($offset && $offset != '.')
      lmb_env_setor('LIMB_HTTP_OFFSET_PATH', $offset . '/');
    else
      lmb_env_setor('LIMB_HTTP_OFFSET_PATH', '');
  }

  if(substr(lmb_env_get('LIMB_HTTP_OFFSET_PATH'), 0, 1) == '/')
    throw new lmbException('LIMB_HTTP_OFFSET_PATH constant must not have starting slash(' . lmb_env_get('LIMB_HTTP_OFFSET_PATH') . ')!!!');

  //HTTP_BASE_PATH is defined automatically according to current host and offset settings
  
  lmb_env_setor('LIMB_HTTP_BASE_PATH', $request->getUri()->toString(
    array('protocol', 'user', 'password', 'host', 'port')). '/' . lmb_env_get('LIMB_HTTP_OFFSET_PATH'));

  if(!lmb_env_has('LIMB_HTTP_GATEWAY_PATH'))
  {
    if(lmb_env_has('LIMB_ENABLE_MOD_REWRITE'))
      lmb_env_setor('LIMB_HTTP_GATEWAY_PATH', lmb_env_get('LIMB_HTTP_BASE_PATH'));
    else
      lmb_env_setor('LIMB_HTTP_GATEWAY_PATH', lmb_env_get('LIMB_HTTP_BASE_PATH') . 'index.php/');
  }

  lmb_env_setor('LIMB_HTTP_SHARED_PATH', lmb_env_get('LIMB_HTTP_BASE_PATH') . 'shared/');

  if(substr(lmb_env_get('LIMB_HTTP_BASE_PATH'), -1, 1) != '/')
  {
    echo('LIMB_HTTP_BASE_PATH constant must have trailing slash(' . lmb_env_get('LIMB_HTTP_BASE_PATH') . ')!!!');
    exit(1);
  }

  if(substr(lmb_env_get('LIMB_HTTP_SHARED_PATH'), -1, 1) != '/')
  {
    echo('LIMB_HTTP_SHARED_PATH constant must have trailing slash(' . lmb_env_get('LIMB_HTTP_SHARED_PATH') . ')!!!');
    exit(1);
  }
}

