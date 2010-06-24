<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbSingleByteCharsetDriver.
 *
 * @package i18n
 * @version $Id: lmbSingleByteCharsetDriver.class.php 7486 2009-01-26 19:13:20Z pachanga $
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
