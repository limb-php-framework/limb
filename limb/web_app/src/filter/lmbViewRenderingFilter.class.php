<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 * class lmbViewRenderingFilter.
 *
 * @package web_app
 * @version $Id: lmbViewRenderingFilter.class.php 7354 2008-12-15 10:50:53Z conf $
 */
class lmbViewRenderingFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
    $response = $toolkit->getResponse();

    if($response->isEmpty() && is_object($view = $toolkit->getView()))
    {
      $view->set('request', $toolkit->getRequest());
      $view->set('session', $toolkit->getSession());
      $view->set('toolkit', $toolkit);
      $response->write($view->render());
    }

    $filter_chain->next();
  }
}


