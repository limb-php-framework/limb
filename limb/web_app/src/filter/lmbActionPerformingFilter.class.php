<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActionPerformingFilter.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

class lmbActionPerformingFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $dispatched = lmbToolkit :: instance()->getDispatchedController();
    if(!is_object($dispatched))
      throw new lmbException('Request is not dispatched yet! lmbDispatchedRequest not found in lmbToolkit!');

    $dispatched->performAction();

    $filter_chain->next();
  }
}

?>