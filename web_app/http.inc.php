<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 */

if(PHP_SAPI == 'cli')
{
  lmb_env_setor('LIMB_HTTP_GATEWAY_PATH', '/');
  lmb_env_setor('LIMB_HTTP_SHARED_PATH', '/shared/');
  lmb_env_setor('LIMB_HTTP_OFFSET_PATH', '');
}
else
{
  $request = lmbToolkit :: instance()->getRequest();

  if(!lmb_env_has('LIMB_HTTP_OFFSET_PATH'))
  {
    $offset = trim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if($offset && $offset != '.')
      lmb_env_setor('LIMB_HTTP_OFFSET_PATH', $offset . '/');
    else
      lmb_env_setor('LIMB_HTTP_OFFSET_PATH', '');
  }
  if(substr(lmb_env_get('LIMB_HTTP_OFFSET_PATH'), 0, 1) == '/')
    throw new lmbException('LIMB_HTTP_OFFSET_PATH constant must not have starting slash(' . lmb_env_get('LIMB_HTTP_OFFSET_PATH') . ')');

  if(!lmb_env_has('LIMB_HTTP_GATEWAY_PATH'))
    lmb_env_setor('LIMB_HTTP_GATEWAY_PATH', lmb_env_get('LIMB_HTTP_OFFSET_PATH') . '/');

  lmb_env_setor('LIMB_HTTP_SHARED_PATH', lmb_env_get('LIMB_HTTP_OFFSET_PATH') . '/shared/');  
  if(substr(lmb_env_get('LIMB_HTTP_SHARED_PATH'), -1, 1) != '/')
    throw new lmbException('LIMB_HTTP_SHARED_PATH constant must have trailing slash(' . lmb_env_get('LIMB_HTTP_SHARED_PATH') . ')');
}

