<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbResponseTransactionFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

class lmbResponseTransactionFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $filter_chain->next();

    $response = lmbToolkit :: instance()->getResponse();
    $response->commit();
  }
}

?>
