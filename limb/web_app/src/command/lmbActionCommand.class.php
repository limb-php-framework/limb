<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActionCommand.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbBaseCommand.class.php');

class lmbActionCommand extends lmbBaseCommand
{
  protected $template_path;

  function __construct($template_path = '')
  {
    parent :: __construct();

    $this->view = $this->toolkit->getView();

    if($template_path)
      $this->template_path = $template_path;
    else
      $this->template_path = $this->_guessTemplatePath();
  }

  protected function _guessTemplatePath()
  {
    $controller = $this->toolkit->getDispatchedController();
    return $controller->getName() . '/' . $controller->getCurrentAction() . '.html';
  }

  function setTemplate($template_path)
  {
    $this->view->setTemplate($template_path);
  }

  function passToView($var, $value)
  {
    $this->view->set($var, $value);
  }

  function resetView()
  {
    $this->view->reset();
  }

  function getTemplatePath()
  {
    return $this->template_path;
  }

  function perform()
  {
    $this->setTemplate($this->template_path);
  }

  function closePopup()
  {
    $this->response->write('<html><script>window.opener.focus();window.opener.location.reload();window.close();</script></html>');
  }
}
?>
