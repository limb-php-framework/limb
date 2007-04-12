<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDelegateList.class.php 5143 2007-02-20 21:40:01Z serega $
 * @package    core
 */

/**
* Used to invoke delegates lists.
* Completely static class.
*/
class lmbDelegateList
{
  /**
  * Invokes all delegates in a list with some args
  * @param array Array of objects that support {@link lmbBaseDelegate} interface
  * @param array Invoke arguments
  */
  static function invokeAll(&$list, $args)
  {
    if (is_object($list))
      $list->invoke($args);
    elseif (is_array($list))
    {
      foreach(array_keys($list) as $key)
        $list[$key]->invoke($args);
    }
  }

  /**
  * Invokes delegates in a list one by one. Stops invoking if delegate return a not null result.
  * @param array Array of objects that support {@link lmbBaseDelegate} interface
  * @param array Invoke arguments
  */
  static function invokeChain(&$list, $args)
  {
    if (is_object($list))
      return $list->invoke($args);
    elseif (is_array($list))
    {
      foreach(array_keys($list) as $key)
      {
        $result = $list[$key]->invoke($args);
        if (!is_null($result))
          return $result;
      }
    }
  }
}
?>
