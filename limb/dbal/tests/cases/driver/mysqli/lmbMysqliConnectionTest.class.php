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

class lmbMysqliConnectionTest extends DriverConnectionTestBase
{
  /**
   * @var lmbMysqliConnection
   */
  var $connection;

  function lmbMysqliConnectionTest()
  {
    parent :: DriverConnectionTestBase('lmbMysqliQueryStatement', 'lmbMysqliInsertStatement', 'lmbMysqliManipulationStatement', 'lmbMysqliStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMysqliSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function getSocket() {
    if (is_string($default_socket = ini_get('mysqli.default_socket'))) {
      return $default_socket;
    }
    if (file_exists($socket = '/var/run/mysqld/mysqld.sock')) {
      return $socket;
    }
    ob_start();
    phpinfo();
    $info = ob_get_clean();

    if (preg_match('/^MYSQLI?_SOCKET => (.*)$/m', $info, $matches)) {
      return trim($matches[1]);
    }
  }

  function testEscape()
  {
    $unescaped_string = "\x00 \n \r \ ' \x1a";
    $escaped_string = $this->connection->escape($unescaped_string);

    try {
      $this->connection->execute('select \''.$unescaped_string.'\';');
      $this->fail();
    } catch (Exception $e)
    {
      $this->pass();
    }

    try {
      $this->connection->execute('select \''.$escaped_string.'\';');
      $this->pass();
    } catch (Exception $e)
    {
      $this->fail();
    }
  }
}
