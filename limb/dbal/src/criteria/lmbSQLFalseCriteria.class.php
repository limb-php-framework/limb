<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLFalseCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

class lmbSQLFalseCriteria extends lmbSQLBaseCriteria
{
  function __construct(){}

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $str .= '1 = 0';
  }
}
?>
