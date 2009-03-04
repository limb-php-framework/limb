<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');

/**
 * class lmbWebApplication.
 *
 * @package web_app
 * @version $Id: lmbWebApplication.class.php 7681 2009-03-04 05:58:40Z pachanga $
 */
class lmbWebApplication extends lmbFilterChain
{
  protected $default_controller_name = "not_found";
  protected $pre_dispatch_filters = array();
  protected $pre_action_filters = array();
  protected $pre_view_filters = array();
  protected $request_dispatcher = null;

  function setDefaultControllerName($name)
  {
    $this->default_controller_name = $name;
  }

  function setRequestDispatcher($disp)
  {
    $this->request_dispatcher = $disp;
  }

  protected function _getRequestDispatcher()
  {
    if(!is_object($this->request_dispatcher))
      return new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher');
    return $this->request_dispatcher;
  }

  function addPreDispatchFilter($filter)
  {
    $this->pre_dispatch_filters[] = $filter;
  }

  function addPreActionFilter($filter)
  {
    $this->pre_action_filters[] = $filter;
  }

  function addPreViewFilter($filter)
  {
    $this->pre_view_filters[] = $filter;
  }

  function process()
  {
    $this->_registerFilters();
    parent :: process();
  }

  protected function _registerFilters()
  {
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbErrorHandlingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));

    $this->_addFilters($this->pre_dispatch_filters);

    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                        array($this->_getRequestDispatcher(), 
                                              $this->default_controller_name)));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));

    $this->_addFilters($this->pre_action_filters);

    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));

    $this->_addFilters($this->pre_view_filters);

    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
  }

  protected function _addFilters($filters)
  {
    foreach($filters as $filter)
      $this->registerFilter($filter);
  }

}


