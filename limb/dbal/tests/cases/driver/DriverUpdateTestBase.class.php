<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverUpdateTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

require_once(dirname(__FILE__) . '/DriverManipTestBase.class.php');

abstract class DriverUpdateTestBase extends DriverManipTestBase
{

  var $manip_stmt_class;

  function DriverUpdateTestBase($manip_stmt_class)
  {
    $this->manip_stmt_class = $manip_stmt_class;
  }

  function testUpdate()
  {
    $sql = "
          UPDATE founding_fathers SET
              first = :first:,
              last = :last:
          WHERE
              id = :id:";
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->manip_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');
    $stmt->setInteger('id', 3);

    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 1);

    $this->checkRecord(3);
  }

  function testAffectedRowCount()
  {
    $sql = "
          UPDATE founding_fathers SET
              first = :first:,
              last = :last:";
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->manip_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 3);
  }
}

?>
