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

/**
 * Base class for all controllers
 *
 * @version $Id: lmbAbstractController.class.php 6243 2007-08-29 11:53:10Z pachanga $
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
   *  Constructor.
   *  Guesses controller {@link $name} if $name attribute is not defined
   */
  function __construct()
  {
    if(!$this->name)
     $this->name = $this->_guessName();

    $this->mixed = new lmbMixable();
    $this->mixed->setOwner($this);
    foreach($this->mixins as $mixin)
      $this->mixed->mixin($mixin);
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
    $template_path = $this->getName() . '/' . $action . '.html';

    $wact_locator = lmbToolkit :: instance()->getWactLocator();

    if($wact_locator->locateSourceTemplate($template_path))
      return $template_path;
    return null;
  }

  static function performCommand()
  {
    $args = func_get_args();
    $class_path = new lmbClassPath(array_shift($args));
    return $class_path->createObject($args)->perform();
  }
}


