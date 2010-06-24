<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

lmb_env_setor('LIMB_SUPPORTED_VIEW_TYPES', '.phtml=lmbMacroView;.html=lmbWactView');

/**
 * class lmbViewTools.
 *
 * @package view
 * @version $Id$
 */
class lmbViewTools extends lmbAbstractTools
{
  protected $view_types = array();
  protected $wact_locator;
  protected $macro_config;
  protected $macro_locator;

  function __construct()
  {
    parent :: __construct();

    $items = explode(';', lmb_env_get('LIMB_SUPPORTED_VIEW_TYPES'));
    foreach($items as $item)
    {
      list($ext, $class) = explode('=', $item);
      $this->view_types[$ext] = $class;
    }
  }

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

    $config = $this->toolkit->getConf('wact');

    $locator = $this->toolkit->getFileLocator($config->get('tpl_scan_dirs'), 'template');
    $this->wact_locator = new lmbWactTemplateLocator($locator, $config->get('cache_dir'));

    return $this->wact_locator;
  }

  function setWactLocator($wact_locator)
  {
    $this->wact_locator = $wact_locator;
  }

  function getMacroConfig()
  {
    if(!$this->macro_config)
    {
      if(!is_object($config = $this->toolkit->getConf('macro')))
        throw new lmbException("Macro configuration not found");

      $this->macro_config = $config;
    }

    return $this->macro_config;
  }

  function getMacroLocator()
  {
    if(is_object($this->macro_locator))
      return $this->macro_locator;

    lmb_require('limb/macro/src/lmbMacroTemplateLocator.class.php');
    lmb_require('limb/macro/src/lmbMacroConfig.class.php');

    $config = lmbToolkit :: instance()->getMacroConfig();
    $this->macro_locator = new lmbMacroTemplateLocator(new lmbMacroConfig($config));

    return $this->macro_locator;
  }

  function setMacroConfig($config)
  {
    $this->macro_config = $config;
  }
}

