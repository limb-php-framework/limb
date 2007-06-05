<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbHandle.class.php');
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');

class lmbDynamicCommandController extends lmbAbstractController
{
  function actionExists($action)
  {
    if($this->_findCommandPathForAction($action))
      return true;

    if($this->_findTemplateForAction($action))
      return true;

    return false;
  }

  function performAction()
  {
    $command = $this->getActionCommand();
    $command->perform();
  }

  function getActionCommand()
  {
    if(!$this->current_action)
      throw new lmbException('Current action is not defined in controller "' . $this->getName());

    if($command_path = $this->_findCommandPathForAction($this->current_action))
      return new lmbHandle($command_path);

    if($template_path = $this->_findTemplateForAction($this->current_action))
      return new lmbHandle('limb/web_app/src/command/lmbActionCommand', array($template_path));

    throw new lmbException('Could not find command class for action "' . $this->current_action . '" in controller "' . $this->getName());
  }

  protected function _findCommandPathForAction($action)
  {
    $path = "src/command/" . $this->getName() . "/" . lmb_camel_case($this->getName() . '_' . $action) . "Command.class.php";
    return lmb_resolve_include_path($path);
  }
}

?>