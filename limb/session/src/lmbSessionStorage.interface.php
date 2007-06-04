<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionStorage.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorage.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function install();
}
?>