<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require(dirname(__FILE__) . '/lmbSQLRawCriteria.class.php');
lmb_require(dirname(__FILE__) . '/lmbSQLFieldBetweenCriteria.class.php');
lmb_require(dirname(__FILE__) . '/lmbSQLFieldCriteria.class.php');

/**
 * class lmbSQLCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLCriteria.class.php 6044 2007-07-02 13:39:54Z pachanga $
 */
class lmbSQLCriteria extends lmbSQLRawCriteria
{
  function __construct($raw_criteria = '', $values = array())
  {
    if(!$raw_criteria)
      $raw_criteria = '1 = 1';

    parent :: __construct($raw_criteria, $values);
  }

  /**
   * Used for chaining
   */
  static function create($raw_criteria = '', $values = array())
  {
    return new lmbSQLCriteria($raw_criteria, $values);
  }

  static function not($criteria)
  {
    return new lmbSQLCriteria('!(' . self :: objectify($criteria)->toStatementString() . ')');
  }

  static function between($column, $value_from, $value_to)
  {
    return new lmbSQLFieldBetweenCriteria($column, $value_from, $value_to);
  }

  static function in($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: IN);
  }

  static function equal($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: EQUAL);
  }

  static function like($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: LIKE);
  }

  static function isNull($column)
  {
    return new lmbSQLFieldCriteria($column, null, lmbSQLFieldCriteria :: IS_NULL);
  }

  static function objectify($args)
  {
    if(is_null($args))
      return new lmbSQLRawCriteria("1 = 1");

    if(is_array($args))
    {
      //array(new lmbSQLRawCriteria(..))
      if(is_object($args[0]))
        return $args[0];

      //array('id=1')
      if(!isset($args[1]) && isset($args[0]))
        return new lmbSQLRawCriteria($args[0]);
      //array('id=?', array(1))
      elseif(isset($args[0]) && is_array($args[1]))
        return new lmbSQLRawCriteria($args[0], $args[1]);
      //array('id=?', 1)
      elseif(isset($args[0]))
      {
        $sql = array_shift($args);
        return new lmbSQLRawCriteria($sql, $args);
      }
    }
    //id=1
    elseif(is_string($args))
    {
      return new lmbSQLRawCriteria($args);
    }
    //new lmbSQLRawCriteria(..)
    elseif(is_object($args))
    {
      return $args;
    }
  }
}
?>
