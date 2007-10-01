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
 * @version $Id: lmbViewRenderingFilter.class.php 6347 2007-10-01 13:17:18Z pachanga $
 */
class lmbViewRenderingFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $toolkit = lmbToolkit :: instance();
    $response = $toolkit->getResponse();

    if(!$response->isEmpty())
    {
      $filter_chain->next();
      return;
    }

    if(is_object($view = $toolkit->getView()))
    {
      $view->set('request', $toolkit->getRequest());
      $view->set('session', $toolkit->getSession());
      $view->set('toolkit', $toolkit);
      $response->write($view->render());
    }

    $filter_chain->next();
  }
}


