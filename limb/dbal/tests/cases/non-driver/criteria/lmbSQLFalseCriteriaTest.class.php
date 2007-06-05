<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFalseCriteria.class.php');

class lmbSQLFalseCriteriaTest extends UnitTestCase
{
  function testAppendStatementTo()
  {
    $c = new lmbSQLFalseCriteria();

    $str = '';
    $c->appendStatementTo($str);
    $this->assertEqual($str, '1 = 0');
  }
}

?>
