<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInterceptingFilter.interface.php 5195 2007-03-06 16:42:37Z pachanga $
 * @package    filter_chain
 */


/**
 * Interface for filter classes what will be used with lmbFilterChain
 *
 * @version $Id: lmbInterceptingFilter.interface.php 5195 2007-03-06 16:42:37Z pachanga $
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