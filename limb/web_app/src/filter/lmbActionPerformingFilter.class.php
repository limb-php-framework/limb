<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActionPerformingFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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