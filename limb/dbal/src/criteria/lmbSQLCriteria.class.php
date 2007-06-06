<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require(dirname(__FILE__) . '/lmbSQLRawCriteria.class.php');

/**
 * class lmbSQLCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLCriteria.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbSQLCriteria extends lmbSQLRawCriteria
{
  function __construct()
  {
    parent :: __construct("1 = 1");
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
