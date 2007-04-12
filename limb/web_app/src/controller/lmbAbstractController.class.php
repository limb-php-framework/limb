<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAbstractController.class.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    web_app
 */

lmb_require('limb/core/src/lmbClassPath.class.php');

/**
 * Base class for all controllers
 *
 * @version $Id: lmbAbstractController.class.php 5645 2007-04-12 07:13:10Z pachanga $
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
   *  Constructor.
   *  Guesses controller {@link $name} if $name attribute is not defined
   */
  function __construct()
  {
    if(!$this->name)
     $this->name = $this->_guessName();
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

?>