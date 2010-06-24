<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverTypeInfoTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqliTypeInfoTest extends DriverTypeInfoTestBase
{

  function lmbMysqliTypeInfoTest()
  {
    parent :: DriverTypeInfoTestBase('lmbMysqliStatement', 'lmbMysqliRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}


