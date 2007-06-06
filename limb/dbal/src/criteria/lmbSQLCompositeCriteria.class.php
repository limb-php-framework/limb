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
 * class lmbSQLCompositeCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLCompositeCriteria.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
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
