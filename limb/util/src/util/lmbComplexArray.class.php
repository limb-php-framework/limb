<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbComplexArray.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */

class lmbComplexArray
{
  function map($map_array, $src_array, &$dest_array)
  {
    foreach($map_array as $src => $dest)
      if(isset($src_array[$src]))
        $dest_array[$dest] = $src_array[$src];
  }

  function arrayMerge($a1, $a2)
  {
    $n = $a1;
    foreach($a2 as $k => $v)
      if(is_array($v) &&  isset($n[$k]) &&  is_array($n[$k]))
        $n[$k] = lmbComplexArray :: arrayMerge($n[$k], $v);
      else
        $n[$k] = $v;
    return $n;
  }

  function explode($pairs_delim, $values_delim, $string)
  {
    $res = array();
    foreach(explode($pairs_delim, $string) as $pair)
    {
      list($key, $value) = explode($values_delim, $pair);
      $res[$key] = $value;
    }
    return $res;
  }

  function & arrayGet($arr_def, &$res_array, $default_value='')
  {
    if($size = sizeof($arr_def))
    {
      $key = array_shift($arr_def);

      if(is_array($res_array) &&  isset($res_array[$key]))
        if($size > 1)
          return lmbComplexArray :: arrayGet($arr_def, $res_array[$key]);
        elseif($size == 1)
          return $res_array[$key];
    }

    return $default_value;
  }

  function arraySet($arr_def, &$res_array, $value)
  {
    if($size = sizeof($arr_def))
    {
      $key = array_shift($arr_def);

      if($size > 1)
      {
        if (!isset($res_array[$key]))
          $res_array[$key] = array();

        lmbComplexArray :: arraySet($arr_def, $res_array[$key], $value);
      }
      elseif($size == 1)
        $res_array[$key] = $value;
    }
  }

  function getColumnValues($column_name, $array)
  {
    $result = array();
    foreach($array as $item)
      $result[] = $item[$column_name];

    return $result;
  }

  function getMaxColumnValue($column_name, $array, &$index)
  {
    $index = 0;

    if(!$values = lmbComplexArray :: getColumnValues($column_name, $array))
      return false;

    $max = max($values);

    $index = array_search($max, $values);

    return $max;
  }

  function getMinColumnValue($column_name, $array, &$index)
  {
    $index = 0;

    if(!$values = lmbComplexArray :: getColumnValues($column_name, $array))
      return false;

    $min = min($values);

    $index = array_search($min, $values);

    return $min;
  }

  function toFlatArray($array, &$result, $prefix='')
  {
    foreach($array as $key => $value)
    {
      $string_key = ($prefix) ? '[' . $key . ']' : $key;

      if(is_array($value))
        lmbComplexArray :: toFlatArray($value, $result, $prefix . $string_key);
      else
        $result[$prefix . $string_key] = $value;
    }
  }

  function arrayMapRecursive($in_func, &$in_array)
  {
    foreach (array_keys($in_array) as $key)
    {
      $value =& $in_array[$key];

      if (is_array($value))
        lmbComplexArray :: arrayMapRecursive($in_func, $value);
      else
        $value = call_user_func_array($in_func, array($value));
    }
    return $in_array;
  }

  //e.g, $sort_params = array('field1' => 'DESC', 'field2' => 'ASC')
  function & sortArray($array, $sort_params, $preserve_keys = true)
  {
    $array_mod = array();
    foreach ($array as $key => $value)
     $array_mod['_' . $key] = $value;

    $i = 0;
    $multi_sort_line = "return array_multisort( ";
    foreach ($sort_params as $name => $sort_type)
    {
     $i++;
     foreach ($array_mod as $row_key => $row)
     {
       if(is_object($row))
         $sort_values[$i][] = $row->get($name);
       else
         $sort_values[$i][] = $row[$name];
     }

     if($sort_type	== 'DESC')
      $sort_args[$i] = SORT_DESC;
     else
      $sort_args[$i] = SORT_ASC;

     $multi_sort_line .= '$sort_values[' . $i . '], $sort_args[' . $i . '], ';
    }
    $multi_sort_line .= '$array_mod );';

    eval($multi_sort_line);

    $array = array();
    foreach($array_mod as $key => $value)
    {
     if($preserve_keys)
      $array[substr($key, 1)] = $value;
     else
      $array[] = $value;
    }

    return $array;
  }
}

?>