<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 * class lmbResponseTransactionFilter.
 *
 * @package web_app
 * @version $Id: lmbResponseTransactionFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbResponseTransactionFilter implements lmbInterceptingFilter
{
  function run($filter_chain)
  {
    $filter_chain->next();

    $response = lmbToolkit :: instance()->getResponse();
    $response->commit();
  }
}


