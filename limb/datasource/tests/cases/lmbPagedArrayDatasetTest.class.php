<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPagedArrayDatasetTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');

class lmbPagedArrayDatasetTest extends UnitTestCase
{
  function testEmpty()
  {
    $iterator = new lmbPagedArrayDataset(array());
    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testIterateWithoutPagination()
  {
    $data = array (array('x' => 1,'y' => 2),
                   array('x' => 3,'y' => 4),
                   array('x' => 5,'y' => 6));

    $iterator = new lmbPagedArrayDataset($data);
    $iterator->rewind();
    $this->assertTrue($iterator->valid());

    $dataspace1 = $iterator->current();
    $this->assertEqual($dataspace1->export(), array('x' => 1,'y' => 2));

    $iterator->next();
    $dataspace2 = $iterator->current();
    $this->assertEqual($dataspace2->export(), array('x' => 3,'y' => 4));
  }

  function testIterateWithPagination()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 0, $limit = 2);
    $this->assertEqual($iterator->count(), 5);
    $this->assertEqual($iterator->countPaginated(), $limit);

    $iterator->rewind();
    $dataspace1 = $iterator->current();
    $this->assertEqual($dataspace1->export(), array('x' => 'a'));
    $iterator->next();
    $dataspace2 = $iterator->current();
    $this->assertEqual($dataspace2->export(), array('x' => 'b'));
  }

  function testIterateWithPaginationNonZeroOffset()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 2, $limit = 2);

    $iterator->rewind();
    $dataspace1 = $iterator->current();
    $this->assertEqual($dataspace1->export(), array('x' => 'c'));
    $iterator->next();
    $dataspace2 = $iterator->current();
    $this->assertEqual($dataspace2->export(), array('x' => 'd'));
  }

  function testPaginateWithOutOfBounds()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 5, $limit = 2);

    $this->assertEqual($iterator->count(), 5);
    $this->assertEqual($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testPaginateWithOffsetLessThanZero()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = -1, $limit = 2);

    $this->assertEqual($iterator->count(), 5);
    $this->assertEqual($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testWorksOkWithArrayOfDataspaces()
  {
    $data = array(new lmbDataspace(array('x' => '1')),
                  new lmbDataspace(array('x' => '2')),
                  new lmbDataspace(array('x' => '3')));

    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 1, $limit = 2);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '23');
  }

  function testResetInternalIteratorIfPrimaryDatasetChanged()
  {
    $data = array(new lmbDataspace(array('x' => '1')),
                  new lmbDataspace(array('x' => '2')),
                  new lmbDataspace(array('x' => '3')));

    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 1, $limit = 3);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '23');

    $iterator->add(new lmbDataspace(array('x' => '4')));

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '234');
  }

  function testResetInternalIteratorOnSortToo()
  {
    $data = array(new lmbDataspace(array('x' => 'C')),
                  new lmbDataspace(array('x' => 'A')),
                  new lmbDataspace(array('x' => 'B')));

    $iterator = new lmbPagedArrayDataset($data);
    $iterator->paginate($offset = 1, $limit = 2);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, 'AB');

    $iterator->sort(array('x' => 'DESC'));

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, 'BA');
  }

}
?>
