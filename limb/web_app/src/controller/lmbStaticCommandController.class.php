<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticCommandController.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');
lmb_require('limb/web_app/src/command/lmbCommand.interface.php');
lmb_require('limb/classkit/src/lmbClassPath.class.php');

class lmbStaticCommandController extends lmbAbstractController
{
  protected $actions;

  function __construct()
  {
    parent :: __construct();

    $this->actions = $this->_defineActions();
  }

  protected function _defineActions()
  {
    return array();
  }

  function getActionProperties($action)
  {
    if(isset($this->actions[$action]))
      return $this->actions[$action];
    else
      return array();
  }

  function getActionProperty($action, $property)
  {
    $props = $this->getActionProperties($action);
    if($props && isset($props[$property]))
      return $props[$property];
    else
      return false;
  }

  function getActionsList()
  {
    return array_keys($this->actions);
  }

  function getActions()
  {
    return $this->actions;
  }

  function actionExists($action)
  {
    return isset($this->actions[$action]);
  }

  function performAction()
  {
    $command = $this->getActionCommand();
    $command->perform();
  }

  function getActionCommand()
  {
    $props = $this->getActionProperties($this->current_action);

    if(!isset($props['command']) || !$props['command'])
      throw new lmbException('Command option is not defined in controller "' .
                             $this->getName(). '" for action "' . $this->current_action . '"');

    $command = $props['command'];

    if(is_object($command) && $command instanceof lmbCommand)
      return $command;

    if(is_object($command) && $command instanceof lmbProxy)
      return $command;

    if(is_array($command))
    {
      $command_path = array_shift($command);
      return lmbClassPath :: create($command_path, $args = $command);
    }

    if(!is_object($command) && !is_array($command))
      return lmbClassPath :: create($command);

    throw new lmbException('Not a valid value for "command" property defined in controller "' .
                           $this->getName(). '" for action "' . $this->current_action . '"');
  }
}

?>