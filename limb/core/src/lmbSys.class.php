<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSys.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
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

?>