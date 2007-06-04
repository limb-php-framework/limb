<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInterceptingFilter.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */


/**
 * Interface for filter classes what will be used with lmbFilterChain
 *
 * @version $Id: lmbInterceptingFilter.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 */
interface lmbInterceptingFilter
{
  /**
   * Runs the filter.
   * Filters should decide whether to pass control to the next filter in the chain or not.
   * @see lmbFilterChain :: next()
   *
   * @param lmbFilterChain filters chain
   * @return void
   */
  function run($filter_chain);
}

?>