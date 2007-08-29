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
 * @version $Id: lmbSessionStorage.interface.php 6243 2007-08-29 11:53:10Z pachanga $
 * @package session
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function install();
}

