<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbMixable.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * Base class for all controllers
 *
 * @version $Id: lmbAbstractController.class.php 6497 2007-11-07 13:27:32Z serega $
 * @package web_app
 */
abstract class lmbAbstractController
{
  /**
   * @var string name of the controller
   */
  protected $name;
  /**
   * @var string default action that will be performed by performAction() if no current_action was speficified
   */
  protected $default_action = 'display';
  /**
   * @var string
   */
  protected $current_action;
  /**
   * @var array array of mixins
   */
  protected $mixins = array();
  /**
   * @var object lmbMixable instance
   */
  protected $mixed;
  /**
   * @var object lmbToolkit instance
   */
  protected $toolkit;
  /**
   * @var array a action to template cached map
   */
  protected $action_template_map = array();
  /**
   * @var boolean
   */
  protected $map_changed = false;

  /**
   *  Constructor.
   *  Guesses controller {@link $name} if $name attribute is not defined
   */
  function __construct()
  {
    $this->toolkit = lmbToolkit :: instance();

    if(!$this->name)
     $this->name = $this->_guessName();

    $this->mixed = new lmbMixable();
    $this->mixed->setOwner($this);
    foreach($this->mixins as $mixin)
      $this->mixed->mixin($mixin);

    $this->_loadCache();
  }

  function __destruct()
  {
    $this->_saveCache();
  }

  function isCacheEnabled()
  {
    return (defined('LIMB_CONTROLLER_CACHE_ENABLED') && constant('LIMB_CONTROLLER_CACHE_ENABLED'));
  }

  function _loadCache()
  {
    if($this->isCacheEnabled() && file_exists($cache = LIMB_VAR_DIR . '/locators/controller_action2tpl.cache'))
      $this->action_template_map = unserialize(file_get_contents($cache));
  }

  function _saveCache()
  {
    if($this->map_changed && $this->isCacheEnabled())
    {
      lmbFs :: safeWrite(LIMB_VAR_DIR . '/locators/controller_action2tpl.cache', 
                         serialize($this->action_template_map));
    }
  }

  /**
   * Using this hacky method mixins can access controller variables
   * @param string variable name
   * @return mixed
   */
  function _get($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  protected function _guessName()
  {
    if($pos = strpos(get_class($this), 'Controller'))
      return lmb_under_scores(substr(get_class($this), 0, $pos));
  }

  function getDefaultAction()
  {
    return $this->default_action;
  }

  /**
   *  Returns {@link $name}
   *  @return string
   */
  function getName()
  {
    return $this->name;
  }

  function setCurrentAction($action)
  {
    $this->current_action = $action;
  }

  function getCurrentAction()
  {
    return $this->current_action;
  }

  abstract function performAction();

  abstract function actionExists($action);

  protected function _findTemplateForAction($action)
  {
    if(isset($this->action_template_map[$this->name]) && isset($this->action_template_map[$this->name][$action]))
      return $this->action_template_map[$this->name][$action];

    $template_format = $this->getName() . '/' . $action . '%s';
    
    if($template_path = $this->_findTemplateByFormat($template_format));
    {
      $this->map_changed = true;
      $this->action_template_map[$this->name][$action] = $template_path;
      return $template_path;
    }

    $this->action_template_map[$this->name][$action] = false;
  }
  
  protected function _findTemplateByFormat($template_format)
  {
    foreach($this->toolkit->getSupportedViewExtensions() as $ext)
    {
      if($template_path = $this->toolkit->locateTemplateByAlias(sprintf($template_format, $ext)))
      {
        return $template_path;
      }
    }
  }

  static function performCommand()
  {
    $args = func_get_args();
    $class_path = new lmbClassPath(array_shift($args));
    return $class_path->createObject($args)->perform();
  }
  
  function forward($controller_name, $action)
  {
    $controller = $this->toolkit->createController($controller_name);
    $controller->setCurrentAction($action);
    return $controller->performAction();
  }

  function forwardTo404()
  {
    return $this->forward('not_found', 'display');
  }

  function forwardTo500()
  {
    return $this->forward('server_error', 'display');
  }
}


