<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbViewRenderingFilter.class.php 5460 2007-04-02 10:30:32Z serega $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

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

    if(is_object($toolkit->getView()))
    {
      $view = $toolkit->getView();
      $view->set('request', $toolkit->getRequest());
      $view->set('session', $toolkit->getSession());
      $view->set('toolkit', $toolkit);
      $response->write($view->render());
    }

    $filter_chain->next();
  }
}

?>
