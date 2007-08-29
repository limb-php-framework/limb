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

  function __construct()
  {
    $this->cache_dir = LIMB_VAR_DIR . '/compiled';
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
  }

  function readTemplateFile($file_name)
  {    
    return file_get_contents($file_name, 1);
  }
}

