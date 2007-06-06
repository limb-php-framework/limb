<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/session/src/lmbSessionStorage.interface.php');

/**
 * lmbSessionNativeStorage does nothing thus keeping native file-based php session storage to be used.
 * @see lmbSessionStartupFilter
 * @version $Id: lmbSessionNativeStorage.class.php 5945 2007-06-06 08:31:43Z pachanga $
 * @package session
 */
class lmbSessionNativeStorage implements lmbSessionStorage
{
  /**
   * Does nothing
   * @see lmbSessionStorage :: install()
   */
  function install(){}
}
?>