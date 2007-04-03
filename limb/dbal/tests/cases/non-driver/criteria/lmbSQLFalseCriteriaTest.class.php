<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLFalseCriteriaTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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
