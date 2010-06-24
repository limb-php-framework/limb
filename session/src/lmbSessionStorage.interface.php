<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Very simple interface for session storage driver classes.
 * @version $Id: lmbSessionStorage.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 * @package session
 */
interface lmbSessionStorage
{
  /**
   * Installs specific session storage functions
   */
  function install();
}

