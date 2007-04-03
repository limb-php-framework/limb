<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMutableDatasetTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbMutableDataset.class.php');

class lmbMutableDatasetTest extends UnitTestCase
{
  protected $dataset;

  function setUp()
  {
    $array = array(array('a'=>'small','b'=>'medium','c'=>'large'),
                   array('a'=>'red','b'=>'green','c'=>'blue'));

    $this->dataset = new lmbMutableDataset($array);
  }

  function testPushRow()
  {
    $row = array('a'=>'1', 'b'=>'2', 'c'=>'3');
    $this->assertEqual(3, $this->dataset->pushRow($row));
    $this->assertEqual($row, $this->dataset->at(2));
  }

  function testPopRow()
  {
    $row = array('a'=>'red', 'b'=>'green', 'c'=>'blue');
    $this->assertEqual($row, $this->dataset->popRow());
  }

  function testUnshiftRow()
  {
    $row = array('a'=>'1', 'b'=>'2', 'c'=>'3');
    $this->assertEqual(3, $this->dataset->unshiftRow($row));
    $this->assertEqual($row, $this->dataset->at(0));
  }

  function testShiftRow()
  {
    $row = array('a'=>'small', 'b'=>'medium', 'c'=>'large');
    $this->assertEqual($row, $this->dataset->shiftRow());
  }

  function testInsertRow()
  {
    $row = array('a'=>'1', 'b'=>'2', 'c'=>'3');
    $this->dataset->rewind();
    $this->dataset->insertRow($row);
    $this->dataset->rewind();
    $this->assertEqual($row, $this->dataset->at(1));
  }

  function testDeleteRow()
  {
    $row = $this->dataset->at(1);
    $deleted = $this->dataset->at(0);
    $this->dataset->rewind();
    $this->assertEqual($this->dataset->deleteRow(), $deleted);
    $this->assertEqual($this->dataset->at(0), $row);
  }

  function testSeekRow()
  {
    $row = array('a'=>'red', 'b'=>'green', 'c'=>'blue');
    $this->assertEqual($row, $this->dataset->seekRow(1));
  }

  function testSeekRows()
  {
    $row0 = array ('a'=>'red', 'b'=>'green','c'=>'blue');
    $row1 = array ('a'=>'one', 'b'=>'two','c'=>'three');
    $row2 = array ('a'=>'z', 'b'=>'y','c'=>'x');
    $row3 = array ('a'=>'1', 'b'=>'2','c'=>'3');
    $array = array (
        $row0, $row1, $row2, $row3
    );
    $M = new lmbMutableDataset($array);
    $this->assertEqual($row0, $M->seekRow(-1));
    $this->assertEqual($row0, $M->seekRow(0));
    $this->assertEqual($row1, $M->seekRow(1));
    $this->assertEqual($row2, $M->seekRow(2));
    $this->assertEqual($row3, $M->seekRow(3));
    $this->assertEqual($row3, $M->seekRow(4));
  }

}
?>
