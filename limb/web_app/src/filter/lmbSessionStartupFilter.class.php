<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/session/src/lmbSession.class.php');


/**
 * Tells lmbSessionStartupFilter filter to use either native (file based) session storage driver (default) or database session storage driver
 * @see lmbSessionStartupFilter
 */
@define('LIMB_SESSION_USE_DB_DRIVER', false);

/**
 * lmbSessionStartupFilter installs session storage driver and starts session.
 *
 * What session storage driver will be used is depend on {@link LIMB_USE_DB_DRIVER} constant value.
 * If LIMB_USE_DB_DRIVER has FALSE value or not defined - native file based session storage will be used.
 * Otherwise database storage driver will be installed.
 * @see lmbSessionNativeStorage
 * @see lmbSessionDbStorage
 *
 * @version $Id: lmbSessionStartupFilter.class.php 6243 2007-08-29 11:53:10Z pachanga $
 * @package web_app
 */
class lmbSessionStartupFilter implements lmbInterceptingFilter
{
  /**
   * @see lmbInterceptingFilter :: run()
   * @uses LIMB_SESSION_USE_DB_DRIVER
   */
  function run($filter_chain)
  {
    if(constant('LIMB_SESSION_USE_DB_DRIVER'))
      $storage =  $this->_createDBSessionStorage();
    else
      $storage =  $this->_createNativeSessionStorage();

    $session = lmbToolkit :: instance()->getSession();
    $session->start($storage);

    $filter_chain->next();
  }

  protected function _createNativeSessionStorage()
  {
    lmb_require('limb/session/src/lmbSessionNativeStorage.class.php');
    return new lmbSessionNativeStorage();
  }

  /**
   * Creates object of {@link lmbSessionDbStorage} class.
   * If constant LIMB_SESSION_DB_MAX_LIFE_TIME is defined passed it's value as session max life time
   * @see lmbInterceptingFilter :: run()
   * @uses LIMB_SESSION_DB_MAX_LIFE_TIME
   */
  protected function _createDBSessionStorage()
  {
    if(defined('LIMB_SESSION_DB_MAX_LIFE_TIME') &&  constant('LIMB_SESSION_DB_MAX_LIFE_TIME'))
      $max_life_time = constant('LIMB_SESSION_DB_MAX_LIFE_TIME');
    else
      $max_life_time = null;

    lmb_require('limb/session/src/lmbSessionDbStorage.class.php');
    $db_connection = lmbToolkit :: instance()->getDefaultDbConnection();
    return new lmbSessionDbStorage($db_connection, $max_life_time);
  }
}

