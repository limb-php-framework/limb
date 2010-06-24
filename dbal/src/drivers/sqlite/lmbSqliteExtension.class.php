<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseExtension.class.php');

/**
 * class lmbSqliteExtension
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteExtension extends lmbDbBaseExtension
{
  function in($column_name, $values)
  {
    return "$column_name IN ('" . implode("','", $values) . "')";
  }

  function concat($values)
  {
    return '(' . implode(' || ', $values) . ')';
  }

  //NOTE:offset leftmost position is 1
  function substr($string, $offset, $limit=null)
  {
    if($limit === null)
      return " SUBSTR($string,$offset,LENGTH($string)-$offset+1) ";
    else
      return " SUBSTR($string,$offset,$limit) ";
  }
}
