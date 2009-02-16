<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
  //we add prefixes in order to avoid possible conflict
  //since variables are added directly
  protected $__config;
  protected $__context;

  function __construct($config, $vars = array())
  {
    $this->__config = $config;    
    $this->setVars($vars);    
  }
  

  //overridden in children
  protected function _init(){}

  function setVars($vars)
  {
    foreach($vars as $name => $value)
      $this->$name = $value;
  }

  function set($name, $value)
  {
    $this->$name = $value;
  }

  function setContext(lmbMacroTemplateExecutor $context)
  {
    $this->__context = $context;
  }

  function __get($name)
  {
    //we can have parent variable context which should be consulted for all missing variables
    //actually, it's quite a dirty hack for a deeper problem which should be addressed later
    if($this->__context)
      return $this->__context->$name;

    //we definitely want to supress warnings, make it some sort of a NullObject?
    return '';
  }

  function render($args = array())
  {
    extract($args);
  }

  function includeTemplate($file, $vars = array(), $slots_handlers = array())
  {
    $template = new lmbMacroTemplate($file, $this->__config);
    $template->setVars(get_object_vars($this));//global template vars
    foreach($slots_handlers as $name => $handlers)
      $template->set('__slot_handlers_' . $name, $handlers);
    
    $template->setChildExecutor($this);//from now we consider the wrapper to be a master variable context
    echo $template->render($vars);//local template vars
  }

  function wrapTemplate($file, $slots_handlers)
  {
    $template = new lmbMacroTemplate($file, $this->__config);
    $template->setVars(get_object_vars($this));//global template vars
    
    foreach($slots_handlers as $name => $handlers)
      $template->set('__slot_handlers_' . $name, $handlers);
    
    $template->setChildExecutor($this);//from now we consider the wrapper to be a master variable context
    echo $template->render();
  }
}

