<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCompositeCriteria.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

class lmbSQLCompositeCriteria extends lmbSQLBaseCriteria
{
  function appendStatementTo(&$str, &$values = array(), $conn = null)
  {
    for($i=0; $i < count($this->clauses); $i++)
    {
      if($i != 0)
        $str .= $this->conjunctions[$i];

      $this->clauses[$i]->appendStatementTo($str, $values, $conn);
    }

    if($i > 1)
      $str = '(' . $str . ')';
  }
}

?>
