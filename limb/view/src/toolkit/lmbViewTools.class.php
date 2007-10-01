<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

@define('LIMB_TEMPLATES_INCLUDE_PATH', 'template;limb/*/template');
@define('LIMB_WACT_TAGS_INCLUDE_PATH', 'src/template/tags;limb/*/src/template/tags;limb/wact/src/tags');
@define('LIMB_MACRO_TAGS_INCLUDE_PATH', 'src/macro;limb/*/src/macro;limb/macro/src/tags');

/**
 * class lmbViewTools.
 *
 * @package view
 * @version $Id$
 */
class lmbViewTools extends lmbAbstractTools
{
  protected $view_types = array('.html' => 'lmbWactView',
                                '.phtml' => 'lmbMacroView');
  protected $wact_locator;
  protected $macro_config;

  function setSupportedViewTypes($types)
  {
    $this->view_types = $types;
  }

  function getSupportedViewTypes()
  {
    return $this->view_types;
  }

  function getSupportedViewExtensions()
  {
    return array_keys($this->view_types);
  }

  function locateTemplateByAlias($alias)
  {
    $class = $this->_findViewClassByTemplate($alias);
    lmb_require("limb/view/src/$class.class.php");
    return call_user_func(array($class, 'locateTemplateByAlias'), $alias);
  }

  function createViewByTemplate($template_name)
  {
    $class = $this->_findViewClassByTemplate($template_name);
    lmb_require("limb/view/src/$class.class.php");
    $view = new $class($template_name);
    return $view;
  }

  protected function _findViewClassByTemplate($template_name)
  {
    $pos = strrpos($template_name, '.');  
    if($pos === false)
      throw new lmbException("Could not determine template type for file '$template_name'");

    $ext = substr($template_name, $pos);

    if(!isset($this->view_types[$ext]))
      throw new lmbException("Template extension '$ext' is not supported");
    
    return $this->view_types[$ext];
  }

  function getWactLocator()
  {
    if(is_object($this->wact_locator))
      return $this->wact_locator;

    lmb_require('limb/view/src/wact/lmbWactTemplateLocator.class.php');

    $locator = $this->toolkit->getFileLocator(LIMB_TEMPLATES_INCLUDE_PATH, 'template');
    $this->wact_locator = new lmbWactTemplateLocator($locator, LIMB_VAR_DIR . '/compiled/');

    return $this->wact_locator;
  }

  function setWactLocator($wact_locator)
  {
    $this->wact_locator = $wact_locator;
  }

  function getMacroConfig()
  {
    if(is_object($this->macro_config))
      return $this->macro_config;

    lmb_require('limb/macro/src/lmbMacroConfig.class.php');
    
    $this->macro_config = new lmbMacroConfig(LIMB_VAR_DIR . '/compiled/', 
                              true,
                              true, 
                              explode(';', LIMB_MACRO_TAGS_INCLUDE_PATH),
                              explode(';', LIMB_TEMPLATES_INCLUDE_PATH));

    return $this->macro_config;
  }

  function setMacroConfig($config)
  {
    $this->macro_config = $config;
  }
}

