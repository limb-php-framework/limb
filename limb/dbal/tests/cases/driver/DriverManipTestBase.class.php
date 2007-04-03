<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverManipTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

abstract class DriverManipTestBase extends UnitTestCase
{

  function checkRecord($id)
  {
    $sql = "SELECT * FROM founding_fathers WHERE id = :id:";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();
    $this->assertNotNull($record);
    if($record)
    {
      $this->assertEqual($record->get('id'), $id);
      $this->assertEqual($record->get('first'), 'Richard');
      $this->assertEqual($record->get('last'), 'Nixon');
    }
  }
}

?>
