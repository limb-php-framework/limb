<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverInsertTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once(dirname(__FILE__) . '/DriverManipTestBase.class.php');

abstract class DriverInsertTestBase extends DriverManipTestBase
{
  var $insert_stmt_class;

  function DriverInsertTestBase($insert_stmt_class)
  {
    $this->insert_stmt_class = $insert_stmt_class;
  }

  function testInsert()
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
    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->checkRecord(4);
  }

  function testInsertId()
  {
    $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->insert_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->assertIdentical($id, 4);
    $this->checkRecord(4);
  }
}
?>
