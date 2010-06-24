<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/session/src/lmbSession.class.php');

/**
 * lmbSessionStartupFilter installs session storage driver and starts session.
 *
 * What session storage driver will be used is depend on {@link LIMB_USE_DB_DRIVER} constant value.
 * If LIMB_USE_DB_DRIVER has FALSE value or not defined - native file based session storage will be used.
 * Otherwise database storage driver will be installed.
 * @see lmbSessionNativeStorage
 * @see lmbSessionDbStorage
 *
 * @version $Id: lmbSessionStartupFilter.class.php 7486 2009-01-26 19:13:20Z pachanga $
 * @package web_app
 */
class lmbSessionStartupFilter implements lmbInterceptingFilter
{
  /**
   * @see lmbInterceptingFilter :: run()
   * @uses LIMB_SESSION_USE_DB_DRIVER
   */
  function run($filter_chain, $session_in_db = false, $session_in_db_lifetime = null)
  {
    if($session_in_db)
      $storage =  $this->_createDBSessionStorage($session_in_db_lifetime);
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
  protected function _createDBSessionStorage($lifetime)
  {
    lmb_require('limb/session/src/lmbSessionDbStorage.class.php');
    $db_connection = lmbToolkit :: instance()->getDefaultDbConnection();
    return new lmbSessionDbStorage($db_connection, $lifetime);
  }
}

