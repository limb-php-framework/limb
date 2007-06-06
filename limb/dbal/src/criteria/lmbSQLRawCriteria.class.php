<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

/**
 * class lmbSQLRawCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLRawCriteria.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbSQLRawCriteria extends lmbSQLBaseCriteria
{
  protected $sql;
  protected $values;

  function __construct($sql, $values = array())
  {
    $this->sql = $sql;
    $this->values = $values;
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $sql = $this->sql;

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

}
?>
