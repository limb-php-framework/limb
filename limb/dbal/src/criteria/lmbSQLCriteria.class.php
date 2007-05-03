<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCriteria.class.php 5790 2007-05-03 07:59:23Z pachanga $
 * @package    dbal
 */
lmb_require(dirname(__FILE__) . '/lmbSQLRawCriteria.class.php');

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
