<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbSys.
 *
 * @package core
 * @version $Id$
 */
class lmbSys
{
  static function isWin32()
  {
    return DIRECTORY_SEPARATOR == '\\';
  }

  static function isUnix()
  {
    return DIRECTORY_SEPARATOR == '/';
  }

  static function isMac()
  {
    return !strncasecmp(PHP_OS, 'MAC', 3);
  }

  static function isModule()
  {
    return !self :: isCgi() && isset($_SERVER['GATEWAY_INTERFACE']);
  }

  static function isCgi()
  {
    return !strncasecmp(PHP_SAPI, 'CGI', 3);
  }

  static function isCli()
  {
    return PHP_SAPI == 'cli';
  }
}


