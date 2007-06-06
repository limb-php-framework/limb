<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class TestAutoTimesObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_auto_times_object';
}

class lmbARAutoTimesTest extends UnitTestCase
{
  var $conn;
  var $db;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    $this->db->delete('test_auto_times_object');
  }

  function testSetTimesAutomaticallyOnCreate()
  {
    $time = time();
    $object = new TestAutoTimesObject();
    $object->setContent('whatever');

    $id = $object->save();

    $object2 = new TestAutoTimesObject($id);
    $this->assertTrue($object2->getUpdateTime() >= $time);
    $this->assertTrue($object2->getCreateTime() >= $time);

    $this->assertEqual($object->getUpdateTime(), $object2->getUpdateTime());
    $this->assertEqual($object->getCreateTime(), $object2->getCreateTime());
  }

  function testSetTimesAutomaticallyOnUpdate()
  {
    $time = time();
    $object = new TestAutoTimesObject();
    $object->setContent('whatever');

    $object->save();
    $ctime1 =  $object->getCreateTime();
    $utime1 =  $object->getUpdateTime();

    sleep(1);

    $object->setContent('whatever2');//without this object is considered to be not dirty
    $object->save();
    $ctime2 =  $object->getCreateTime();
    $utime2 =  $object->getUpdateTime();

    $this->assertTrue($ctime1 >= $time);
    $this->assertTrue($utime1 >= $time);
    $this->assertEqual($ctime1, $ctime2);
    $this->assertTrue($utime2 > $utime1);

    $object2 = new TestAutoTimesObject($object->getId());
    $this->assertEqual($object2->getCreateTime(), $ctime1);
    $this->assertEqual($object2->getUpdateTime(), $utime2);
  }
}
?>
