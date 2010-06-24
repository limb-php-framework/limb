<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollection.class.php');

class lmbCollectionPaginationTest extends UnitTestCase
{
  function testIterateWithPagination()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbCollection($data);
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
    $iterator = new lmbCollection($data);
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
    $iterator = new lmbCollection($data);
    $iterator->paginate($offset = 5, $limit = 2);

    $this->assertEqual($iterator->count(), 5);
    $this->assertEqual($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testPaginateWithOffsetLessThanZero()
  {
    $data = array(array('x' => 'a'), array('x' => 'b'), array('x' => 'c'), array('x' => 'd'), array('x' => 'e'));
    $iterator = new lmbCollection($data);
    $iterator->paginate($offset = -1, $limit = 2);

    $this->assertEqual($iterator->count(), 5);
    $this->assertEqual($iterator->countPaginated(), 0);

    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function testWorksOkWithArrayOfSets()
  {
    $data = array(new lmbSet(array('x' => '1')),
                  new lmbSet(array('x' => '2')),
                  new lmbSet(array('x' => '3')));

    $iterator = new lmbCollection($data);
    $iterator->paginate($offset = 1, $limit = 2);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '23');
  }

  function testResetInternalIteratorIfPrimaryDatasetChanged()
  {
    $data = array(new lmbSet(array('x' => '1')),
                  new lmbSet(array('x' => '2')),
                  new lmbSet(array('x' => '3')));

    $iterator = new lmbCollection($data);
    $iterator->paginate($offset = 1, $limit = 3);

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '23');

    $iterator->add(new lmbSet(array('x' => '4')));

    $str = '';
    foreach($iterator as $record)
      $str .= $record->get('x');

    $this->assertEqual($str, '234');
  }

  function testResetInternalIteratorOnSortToo()
  {
    $data = array(new lmbSet(array('x' => 'C')),
                  new lmbSet(array('x' => 'A')),
                  new lmbSet(array('x' => 'B')));

    $iterator = new lmbCollection($data);
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

