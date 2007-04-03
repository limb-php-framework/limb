<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbBaseCommand.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
require_once('limb/web_app/toolkit.inc.php');
lmb_require('limb/web_app/src/command/lmbCommand.interface.php');

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
