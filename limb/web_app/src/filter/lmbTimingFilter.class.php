<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTimingFilter.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

class lmbTimingFilter implements lmbInterceptingFilter
{
  public function run($filter_chain)
  {
    $start_time = microtime(true);

    $filter_chain->next();

    echo '<small>' . round(microtime(true) - $start_time, 2) . '</small>';
  }
}

?>