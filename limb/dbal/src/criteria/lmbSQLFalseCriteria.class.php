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
 * class lmbSQLFalseCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLFalseCriteria.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbSQLFalseCriteria extends lmbSQLBaseCriteria
{
  function __construct(){}

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $str .= '1 = 0';
  }
}
?>
