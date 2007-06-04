<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLTrueCriteria.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

class lmbSQLTrueCriteria extends lmbSQLBaseCriteria
{
  function __construct(){}

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $str .= '1 = 1';
  }
}
?>
