<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreePath.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

class lmbTestTreePath
{
  static function normalize($tests_path)
  {
    return '/' . implode('/', self :: toArray($tests_path));
  }

  static function toArray($tests_path)
  {
    $tests_path = preg_replace('~\/\/+~', '/', $tests_path);
    $tests_path = rtrim($tests_path, '/');
    $path_array = explode('/', $tests_path);

    if(isset($path_array[0]) && $path_array[0] == '')
      array_shift($path_array);

    $new_array = array();
    foreach($path_array as $item)
    {
      if($item == '..')
        array_pop($new_array);
      else
        $new_array[] = $item;
    }
    return $new_array;
  }
}

?>
