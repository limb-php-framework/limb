<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverConnectionTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlConnectionTest extends DriverConnectionTestBase
{

  function lmbPgsqlConnectionTest()
  {
    parent :: DriverConnectionTestBase('lmbPgsqlQueryStatement', 'lmbPgsqlInsertStatement', 'lmbPgsqlManipulationStatement', 'lmbPgsqlStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


