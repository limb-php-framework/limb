<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroTemplateExecutor.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplateExecutor
{
  protected $cache_dir;
  protected $locator;

  function __construct($vars = array(), $cache_dir = null, $locator = null)
  {
    foreach($vars as $name => $value)
      $this->$name = $value;

    $this->cache_dir = $cache_dir;
    $this->locator = $locator;
  }

  function set($name, $value)
  {
    $this->$name = $value;
  }

  function __get($name)
  {
    //we definitely want to supress warnings, make it some sort of a NullObject?
    return '';
  }

  function render($args = array())
  {
    extract($args);
  }

  function includeTemplate($file, $vars = array())
  {
    $template = new lmbMacroTemplate($file, $this->cache_dir, $this->locator);
    $template->setVars(get_object_vars($this));//global template vars
    echo $template->render($vars);//local template vars
  }
}

