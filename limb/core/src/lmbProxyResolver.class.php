<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbProxyResolver.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */

class lmbProxyResolver
{
  static function resolve($proxy)
  {
    return lmbProxyResolver :: _doResolve($proxy);
  }

  static function _doResolve($obj, &$traversed = array())
  {
    if(!is_object($obj))
       return $obj;

    //lines below help preventing recursions
    $hash = lmbProxyResolver :: _getObjectID($obj);
    if(isset($traversed[$hash]))
      return $obj;
    else
      $traversed[$hash] = true;

    if($obj instanceof lmbProxyable)
      $resolved = $obj->resolve();
    else
      $resolved = $obj;//use clone?

    if(!$vars = get_object_vars($resolved))
      return $resolved;

    foreach(array_keys($vars) as $key)
    {
      $attr =& $resolved->$key;
      if(is_array($attr))
      {
        foreach(array_keys($attr) as $arr_key)
        {
          $attr[$arr_key] = lmbProxyResolver :: _doResolve($attr[$arr_key], $traversed);
        }
      }
      else
      {
        $attr = lmbProxyResolver :: _doResolve($attr, $traversed);
      }
    }
    return $resolved;
  }

  static function _getObjectID($obj)
  {
    preg_match('~^[^#]+#(\d+)~', "$obj", $m);
    return get_class($obj) . $m[1];
  }
}

?>
