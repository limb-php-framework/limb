<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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

  function __construct($config)
  {
    $this->config = $config;
  }

  function locateSourceTemplate($file_name)
  {                
    $file_path = $this->config['tpl_scan_dirs'].'/'.$file_name;
    if(!file_exists($file_path))
      throw new lmbMacroException('template file not found', array('template' => $file_path));
    return $file_path;
  }

  function locateCompiledTemplate($file_name)
  {
    return $this->config['cache_dir'] . '/' . md5($file_name) . '.php';
  }
}

