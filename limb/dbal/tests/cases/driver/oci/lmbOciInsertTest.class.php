<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciInsertTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once(dirname(__FILE__) . '/../DriverInsertTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciInsertTest extends DriverInsertTestBase
{
  function lmbOciInsertTest()
  {
    parent :: DriverInsertTestBase('lmbOciInsertStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function testInsertIdShouldUseSequence()
  {
    $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertTrue($id > 0);

    $this->connection->newStatement("DELETE FROM founding_fathers")->execute();

    $new_id = $stmt->insertId('id');
    $this->assertEqual($new_id - $id, 1);
  }
}

?>
