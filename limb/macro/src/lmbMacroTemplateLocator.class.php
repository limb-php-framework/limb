<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroTemplateLocator.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateLocator
{
  protected $cache_dir;
  protected $base_dir;

  function __construct($base_dir = null, $cache_dir = null)
  {
    if(!$cache_dir)
      $cache_dir = LIMB_VAR_DIR . '/compiled';

    $this->cache_dir = $cache_dir;
    $this->base_dir = $base_dir;
  }

  function locateCompiledTemplate($file_name)
  {
    return $this->cache_dir . '/' . md5($file_name) . '.php';
  }

  function locateSourceTemplate($file_name)
  {    
    //fix this later
    if(lmbFs :: isPathAbsolute($file_name))
      return $file_name;
    else
      return $this->base_dir . '/' . $file_name;
  }

  function readTemplateFile($file_name)
  {    
    return file_get_contents($file_name);
  }
}

