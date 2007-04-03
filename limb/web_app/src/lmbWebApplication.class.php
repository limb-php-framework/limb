<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebApplication.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/classkit/src/lmbHandle.class.php');

class lmbWebApplication extends lmbFilterChain
{
  function __construct()
  {
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                        array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'))));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
    $this->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));
  }
}

?>
