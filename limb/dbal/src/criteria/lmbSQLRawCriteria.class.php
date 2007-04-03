<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLRawCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

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
