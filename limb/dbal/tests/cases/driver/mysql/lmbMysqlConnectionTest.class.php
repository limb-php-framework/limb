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

class lmbMysqlConnectionTest extends DriverConnectionTestBase
{
  protected $query_stmt_class = 'lmbMysqlQueryStatement';
  protected $insert_stmt_class = 'lmbMysqlInsertStatement';
  protected $manip_stmt_class = 'lmbMysqlManipulationStatement';
  protected $default_stmt_class = 'lmbMysqlStatement';

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMysqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function getSocket() {
    if (is_string($default_socket = ini_get('mysql.default_socket'))) {
      return $default_socket;
    }
    if (file_exists($socket = '/var/run/mysqld/mysqld.sock')) {
      return $socket;
    }
    ob_start();
    phpinfo();
    $info = ob_get_clean();

    if (preg_match('/^MYSQL?_SOCKET => (.*)$/m', $info, $matches)) {
      return trim($matches[1]);
    }
  }
}

