<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require(dirname(__FILE__) . '/lmbSQLBaseCriteria.class.php');
lmb_require(dirname(__FILE__) . '/lmbSQLFieldBetweenCriteria.class.php');
lmb_require(dirname(__FILE__) . '/lmbSQLFieldCriteria.class.php');

/**
 * class lmbSQLCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLCriteria.class.php 8071 2010-01-20 08:30:55Z korchasa $
 */
class lmbSQLCriteria extends lmbSQLBaseCriteria
{
  protected $raw_sql;
  protected $values;

  function __construct($raw_sql = '1 = 1', $values = array())
  {
    $this->raw_sql = $raw_sql;
    $this->values = $values;
  }
  /**
   * Used for chaining
   * @return lmbSQLCriteria
   */
  static function create($raw_sql = '1 = 1', $values = array())
  {
    return new lmbSQLCriteria($raw_sql, $values);
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $sql = $this->raw_sql;

    foreach($this->values as $key => $value)
    {
      if(is_numeric($key))
      {
        $random_key = 'p' . mt_rand();
        $values[$random_key] = $value;
        $sql = preg_replace('~\?~', ':' . $random_key . ':', $sql, $limit = 1);
      }
      else
        $values[$key] = $value;
    }

    $str .= $sql;
  }

  /**
   * @param string $column
   * @param string $value_from
   * @param string $value_to
   * @return lmbSQLFieldBetweenCriteria
   */
  static function between($column, $value_from, $value_to)
  {
    return new lmbSQLFieldBetweenCriteria($column, $value_from, $value_to);
  }

  /**
   * @param string $column
   * @param string $value
   * @param string $handler
   * @return lmbSQLFieldCriteria
   */
  static function in($column, $values, $handler = null)
  {
    if($handler)
      $values = array_map($handler, $values);
    return new lmbSQLFieldCriteria($column, $values, lmbSQLFieldCriteria :: IN);
  }

  /**
   * @param string $column
   * @param string $value
   * @return lmbSQLFieldCriteria
   */
  static function equal($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: EQUAL);
  }

  /**
   * @param string $column
   * @param string $value
   * @return lmbSQLFieldCriteria
   */
  static function greater($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: GREATER);
  }

  /**
   * @param string $column
   * @param string $value
   * @return lmbSQLFieldCriteria
   */
  static function less($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: LESS);
  }

  /**
   * @param string $column
   * @param string $value
   * @return lmbSQLFieldCriteria
   */
  static function like($column, $value)
  {
    return new lmbSQLFieldCriteria($column, $value, lmbSQLFieldCriteria :: LIKE);
  }

  /**
   * @param string $column
   * @return lmbSQLFieldCriteria
   */
  static function isNull($column)
  {
    return new lmbSQLFieldCriteria($column, null, lmbSQLFieldCriteria :: IS_NULL);
  }

  /**
   * @param mixed $args
   * @return lmbSQLCriteria
   */
  static function objectify($args)
  {
    if(is_null($args))
      return new lmbSQLCriteria();

    if(is_array($args))
    {
      //array(new lmbSQLCriteria(..))
      if(isset($args[0]) && is_object($args[0]))
        return $args[0];

      //array('id=1')
      if(!isset($args[1]) && isset($args[0]))
        return new lmbSQLCriteria($args[0]);

      //array('id=?', array(1))
      if(isset($args[0]) && is_array($args[1]))
        return new lmbSQLCriteria($args[0], $args[1]);

      //array('id=?', 1)
      if(isset($args[0]))
      {
        $sql = array_shift($args);
        return new lmbSQLCriteria($sql, $args);
      }

      throw new lmbDbException("Unsupported criteria format passed as array", array('criteria' => $args));
    }
    //id=1
    elseif(is_string($args))
    {
      return new lmbSQLCriteria($args);
    }
    //new lmbSQLCriteria(..)
    elseif(is_object($args))
    {
      return $args;
    }
  }
}

