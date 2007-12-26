<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

@define('LIMB_CONTROLLERS_INCLUDE_PATH', 'src/controller;limb/*/src/controller');

/**
 * class lmbWebAppTools.
 *
 * @package web_app
 * @version $Id: lmbWebAppTools.class.php 6662 2007-12-26 08:54:19Z serega $
 */
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

    //default view
    lmb_require('limb/view/src/lmbDummyView.class.php');
    $this->view = new lmbDummyView('dummy_template_path');

    return $this->view;
  }

  function setDispatchedController($dispatched)
  {
    $this->dispatched_controller = $dispatched;
  }

  function getDispatchedController()
  {
    return $this->dispatched_controller;
  }

  function getRoutesUrl($params = array(), $route_name = '', $skip_controller = false)
  {
    $routes = $this->toolkit->getRoutes();
    if(!isset($params['controller']) && !$skip_controller)
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
    if(!class_exists($class_name))
    {
      $file = $this->toolkit->findFileByAlias("$class_name.class.php", LIMB_CONTROLLERS_INCLUDE_PATH, 'controller');
      lmb_require($file);
    }
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

