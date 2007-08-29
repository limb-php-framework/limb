<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 * class lmbRequestDispatchingFilter.
 *
 * @package web_app
 * @version $Id: lmbRequestDispatchingFilter.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbRequestDispatchingFilter implements lmbInterceptingFilter
{
  protected $dispatcher;
  protected $default_controller_name;

  function __construct($dispatcher, $default_controller_name = 'not_found')
  {
    $this->dispatcher = $dispatcher;
    $this->setDefaultControllerName($default_controller_name);
  }

  function setDefaultControllerName($default_controller_name)
  {
    $this->default_controller_name = $default_controller_name;
  }

  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();

    $dispatched_params = $this->dispatcher->dispatch($toolkit->getRequest());

    $controller = $this->_createController($dispatched_params);

    if(isset($dispatched_params['action']) && $controller->actionExists($dispatched_params['action']))
      $controller->setCurrentAction($dispatched_params['action']);
    elseif(!isset($dispatched_params['action']))
      $controller->setCurrentAction($controller->getDefaultAction());
    else
      $controller = $this->_createDefaultController();

    $toolkit->setDispatchedController($controller);

    $this->_putOtherParamsToRequest($dispatched_params);

    $filter_chain->next();
  }

  protected function _createController($dispatched_params)
  {
    if(!isset($dispatched_params['controller']))
      $dispatched_params['controller'] = $this->default_controller_name;

    try
    {
      $controller = lmbToolkit :: instance()->createController($dispatched_params['controller']);
    }
    catch(lmbException $e)
    {
      $controller = lmbToolkit :: instance()->createController($this->default_controller_name);
    }

    return $controller;
  }

  protected function _createDefaultController()
  {
    $controller = lmbToolkit :: instance()->createController($this->default_controller_name);
    $controller->setCurrentAction($controller->getDefaultAction());
    return $controller;
  }

  protected function _putOtherParamsToRequest($dispatched_params)
  {
    lmbToolkit :: instance()->getRequest()->merge($dispatched_params);
  }
}


