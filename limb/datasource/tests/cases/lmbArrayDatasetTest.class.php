<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbArrayDatasetTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');

class lmbArrayDatasetTest extends UnitTestCase
{
  function testEmptyDataset()
  {
    $iterator = new lmbArrayDataset(array());
    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testIterate()
  {
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4),
                   array('x' => 5,'y' => 6));

    $iterator = new lmbArrayDataset($data);
    $iterator->rewind();
    $this->assertTrue($iterator->valid());

    $dataspace1 = $iterator->current();
    $this->assertEqual($dataspace1->export(), array('x' => 1,'y' => 2));

    $iterator->next();
    $dataspace2 = $iterator->current();
    $this->assertEqual($dataspace2->export(), array('x' => 3,'y' => 4));
  }

  function testIterateOver()
  {
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4));
    $iterator = new lmbArrayDataset($data);
    $iterator->rewind();
    $iterator->next();
    $iterator->next();
    $this->assertFalse($iterator->valid());
    $dataspace = $iterator->current();
    $this->assertEqual($dataspace->export(), array());
  }

  function testIterateWithForeach()
  {
    $data = array (array('x' => '1'),
                   array('x' => '2'),
                   array('x' => '3'));

    $iterator = new lmbArrayDataset($data);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '123');
  }

  function testWorksOkWithArrayOfDataspaces()
  {
    $data = array(new lmbDataspace(array('x' => '1')),
                  new lmbDataspace(array('x' => '2')),
                  new lmbDataspace(array('x' => '3')));

    $iterator = new lmbArrayDataset($data);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '123');
  }

  function testAdd()
  {
    $item1 = new lmbDataspace(array('x' => 1,'y' => 2));
    $item2 = new lmbDataspace(array('x' => 3,'y' => 4));

    $iterator = new lmbArrayDataset();
    $this->assertTrue($iterator->isEmpty());
    $iterator->add($item1);
    $this->assertFalse($iterator->isEmpty());
    $iterator->add($item2);

    $iterator->rewind();
    $this->assertTrue($iterator->valid());

    $this->assertEqual($iterator->current(), $item1);
    $iterator->next();
    $this->assertEqual($iterator->current(), $item2);
  }

  function testSort()
  {
    $data = array (array('x' => 'C'),
                   array('x' => 'A'),
                   array('x' => 'B'));

    $iterator = new lmbArrayDataset($data);
    $iterator->sort(array('x' => 'DESC'));
    $arr = $iterator->getArray();
    $this->assertEqual($arr[0]['x'], 'C');
    $this->assertEqual($arr[1]['x'], 'B');
    $this->assertEqual($arr[2]['x'], 'A');
  }

  function testSortWorksOkWithDataspacesToo()
  {
    $item1 = new lmbDataspace(array('x' => 'C'));
    $item2 = new lmbDataspace(array('x' => 'A'));
    $item3 = new lmbDataspace(array('x' => 'B'));

    $iterator = new lmbArrayDataset(array($item1, $item2, $item3));
    $iterator->sort(array('x' => 'DESC'));
    $arr = $iterator->getArray();
    $this->assertEqual($arr[0]->get('x'), 'C');
    $this->assertEqual($arr[1]->get('x'), 'B');
    $this->assertEqual($arr[2]->get('x'), 'A');
  }
}
?>
