<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverRecordTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

abstract class DriverRecordTestBase extends UnitTestCase
{
  var $record_class;

  function __construct($record_class)
  {
    $this->record_class = $record_class;
  }

  function testArrayAccessImplementation()
  {
    $record = new $this->record_class(array('test' => 'value'));
    $this->assertEqual($record['test'], 'value');
  }
}

?>
