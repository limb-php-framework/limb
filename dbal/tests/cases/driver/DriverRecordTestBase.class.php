<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
  
  function testGetWithDefaultValue()
  {
    $record = new $this->record_class();
    $this->assertEqual($record->get('foo'), null);
    $this->assertEqual($record->get('foo', 'bar'), 'bar');
  }
  
  function testImplementsIterator()
  {
    $set = new lmbObject($array = array(
      'test1' => 'foo',
      'test2' => 'bar',
    ));
    $result = array();
    foreach($set as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($array, $result);
  }

  function testImplementsIteratorWithFalseElementsInArray()
  {
    $set = new lmbObject($array = array(
      'test1' => 'foo',
      'test2' => false,
      'test3' => 'bar'
    ));    
    $result = array();
    foreach($set as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($array, $result);
  }
}


