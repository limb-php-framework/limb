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

class lmbSqliteConnectionTest extends DriverConnectionTestBase
{
  protected $query_stmt_class = 'lmbSqliteQueryStatement';
  protected $insert_stmt_class = 'lmbSqliteInsertStatement';
  protected $manip_stmt_class = 'lmbSqliteManipulationStatement';
  protected $default_stmt_class = 'lmbSqliteStatement';

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverSqliteSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function testSocketConnection() {}
}


