<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppTools.class.php 5532 2007-04-05 10:31:47Z pachanga $
 * @package    web_app
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

@define('LIMB_CONTROLLERS_INCLUDE_PATH', 'src/controller;limb/*/src/controller');

class lmbWebAppTools extends lmbAbstractTools
{
  protected $session;
  protected $view;
  protected $dispatched_controller;
  protected $routes;
  protected $flash_box;

  function getSession()
  {
    if(is_object($this->session))
      return $this->session;

    lmb_require('limb/session/src/lmbSession.class.php');
    $this->session = new lmbSession();

    return $this->session;
  }

  function setSession($session)
  {
    $this->session = $session;
  }

  function setView($view)
  {
    $this->view = $view;
  }

  function getView()
  {
    if(is_object($this->view))
      return $this->view;

    lmb_require('limb/view/src/lmbWactView.class.php');
    $this->view = new lmbWactView();

    return $this->view;
  }

  function renderView($template)
  {
    $view = $this->toolkit->getView();
    $response = $this->toolkit->getResponse();
    $view->setTemplate($template);
    $response->write($view->render());
  }

  function setDispatchedController($dispatched)
  {
    $this->dispatched_controller = $dispatched;
  }

  function getDispatchedController()
  {
    return $this->dispatched_controller;
  }

  function getRoutesUrl($params = array(), $route_name = '')
  {
    $routes = $this->toolkit->getRoutes();
    if(!isset($params['controller']))
      $params['controller'] = $this->toolkit->getDispatchedController()->getName();

    return LIMB_HTTP_GATEWAY_PATH . ltrim($routes->toUrl($params, $route_name), '/');
  }

  function getRoutes()
  {
    if(!$this->routes)
    {
      $config = $this->toolkit->getConf('routes');

      lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
      $this->routes = new lmbRoutes($config->export());
    }

    return $this->routes;
  }

  function setRoutes($routes)
  {
    $this->routes = $routes;
  }

  function getFlashBox()
  {
    if(!is_object($this->flash_box))
    {
      lmb_require('limb/web_app/src/util/lmbFlashBox.class.php');
      $this->flash_box = lmbFlashBox :: create($this->toolkit->getSession());
    }

    return $this->flash_box;
  }

  function flashError($message)
  {
    $this->toolkit->getFlashBox()->addError($message);
  }

  function flashMessage($message)
  {
    $this->toolkit->getFlashBox()->addMessage($message);
  }

  function createController($controller_name)
  {
    $class_name = lmb_camel_case($controller_name) . 'Controller';
    $file = $this->toolkit->findFileAlias("$class_name.class.php", LIMB_CONTROLLERS_INCLUDE_PATH, 'controller');
    lmb_require($file);
    return new $class_name;
  }

  function redirect($params_or_url = array(), $route_url = null, $append = '')
  {
    $toolkit = $this->toolkit;

    if(is_array($params_or_url))
      $toolkit->getResponse()->redirect($toolkit->getRoutesUrl($params_or_url, $route_url) . $append);
    else
      $toolkit->getResponse()->redirect($params_or_url . $append);
  }
}
?>
