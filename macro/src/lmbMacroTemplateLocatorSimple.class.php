<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/macro/src/lmbMacroTemplateLocatorInterface.interface.php');
/**
 * class lmbMacroSimpleTemplateLocator.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateLocatorSimple implements lmbMacroTemplateLocatorInterface
{
  protected $config;

  function __construct(lmbMacroConfig $config)
  {
    $this->config = $config;
  }

  function locateSourceTemplate($file_name)
  {         
    if(lmb_is_path_absolute($file_name))
      return $file_name;
    
    $dirs = $this->config->tpl_scan_dirs;
      
    foreach($dirs as $dir)
    {
      $file_path = $dir . '/' . $file_name;
      if(lmb_is_path_absolute($file_path) && file_exists($file_path))
        return $file_path;
      if($full_path = lmb_resolve_include_path($file_path))
        return $full_path;
    }
    
    throw new lmbMacroException('template file not found', array('template' => $file_path));
  }

  function locateCompiledTemplate($file_name)
  {
    return $this->config->cache_dir . '/' . lmbMacroTemplate::encodeCacheFileName($file_name);
  }
}

