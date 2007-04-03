<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSessionStorage.interface.php 5196 2007-03-06 17:46:02Z pachanga $
 * @package    session
 */

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorage.interface.php 5196 2007-03-06 17:46:02Z pachanga $
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function install();
}
?>