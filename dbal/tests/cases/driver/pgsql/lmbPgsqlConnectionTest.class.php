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
  protected $query_stmt_class = 'lmbPgsqlQueryStatement';
  protected $insert_stmt_class = 'lmbPgsqlInsertStatement';
  protected $manip_stmt_class = 'lmbPgsqlManipulationStatement';
  protected $default_stmt_class = 'lmbPgsqlStatement';

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}


