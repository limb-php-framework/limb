<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSingleByteCharsetDriver.class.php 5380 2007-03-28 12:16:15Z pachanga $
 * @package    i18n
 */

class lmbSingleByteCharsetDriver
{
  function __call($method, $args)
  {
    $func = substr($method, 1);
    return call_user_func_array($func, $args);
  }

  function _preg_match($pattern, $subject, &$matches, $flags=null, $offset=null)
  {
    if(!is_null($flags) && !is_null($offset))
      return preg_match($pattern, $subject, $matches, $flags, $offset);
    elseif (is_null($flags) && !is_null($offset))
      return preg_match($pattern, $subject, $matches, $flags);
    else
      return preg_match($pattern, $subject, $matches);
  }

  function _preg_match_all($pattern, $subject, &$matches, $flags=null, $offset=null)
  {
    if(!is_null($flags) && !is_null($offset))
      return preg_match_all($pattern, $subject, $matches, $flags, $offset);
    elseif (is_null($flags) && !is_null($offset))
      return preg_match_all($pattern, $subject, $matches, $flags);
    else
      return preg_match_all($pattern, $subject, $matches);
  }
}
