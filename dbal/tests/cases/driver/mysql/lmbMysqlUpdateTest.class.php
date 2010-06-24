<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverUpdateTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlUpdateTest extends DriverUpdateTestBase
{

  function lmbMysqlUpdateTest()
  {
    parent :: DriverUpdateTestBase('lmbMysqlManipulationStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMysqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


