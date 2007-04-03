<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlConnectionTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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

?>