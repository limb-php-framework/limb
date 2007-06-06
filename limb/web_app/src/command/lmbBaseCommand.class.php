<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/web_app/toolkit.inc.php');
lmb_require('limb/web_app/src/command/lmbCommand.interface.php');

/**
 * abstract class lmbBaseCommand.
 *
 * @package web_app
 * @version $Id: lmbBaseCommand.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
abstract class lmbBaseCommand implements lmbCommand
{
  protected $toolkit;
  protected $request;
  protected $response;
  protected $session;

  function __construct()
  {
    $this->toolkit = lmbToolkit :: instance();
    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
  }

  static function performCommand()
  {
    $args = func_get_args();
    $class_path = new lmbClassPath(array_shift($args));
    return $class_path->createObject($args)->perform();
  }

  function redirect($params_or_url = array(), $route_url = null)
  {
    $this->toolkit->redirect($params_or_url, $route_url);
  }

  function flashError($message)
  {
    $this->toolkit->flashError($message);
  }

  function flashMessage($message)
  {
    $this->toolkit->flashMessage($message);
  }

  function flash($message)
  {
    $this->flashMessage($message);
  }
}
?>
