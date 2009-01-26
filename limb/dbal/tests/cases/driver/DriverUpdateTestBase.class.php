<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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


