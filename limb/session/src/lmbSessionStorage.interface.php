<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorage.interface.php 5942 2007-06-05 19:22:26Z pachanga $
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function install();
}
?>