<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverRecordTestBase.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
