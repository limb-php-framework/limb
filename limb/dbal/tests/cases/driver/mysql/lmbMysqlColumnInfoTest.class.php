<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlColumnInfoTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once(dirname(__FILE__) . '/../DriverColumnInfoTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlColumnInfoTest extends DriverColumnInfoTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMysqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}

?>
