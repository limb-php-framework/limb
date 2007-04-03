<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARManyToManyCollectionTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbARManyToManyCollection.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordManyToManyRelationsTest.class.php');

Mock :: generate('GroupForTest', 'MockGroupForTest');

class GroupForTestStub extends GroupForTest
{
  var $save_calls = 0;

  function save()
  {
    parent :: save();
    $this->save_calls++;
  }
}

class lmbARManyToManyCollectionTest extends UnitTestCase
{
  protected $db;

  function setUp()
  {
    $this->db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    lmbActiveRecord :: delete('GroupForTest');
    lmbActiveRecord :: delete('UserForTest');
  }

  function testAddToWithExistingOwner()
  {
    $user = $this->_createUserAndSave();

    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $arr = $collection->getArray();

    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEqual(sizeof($arr), 2);

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection2->getArray();

    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEqual(sizeof($arr), 2);
  }

  function testAddToWithNonSavedOwner()
  {
    $user = $this->_initUser();

    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $arr = $collection->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection2->getArray();

    $this->assertEqual(sizeof($arr), 0);
  }

  function testSaveWithExistingOwnerDoesNothing()
  {
    $group1 = new MockGroupForTest();
    $group2 = new MockGroupForTest();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection->add($group1);
    $collection->add($group2);

    $group1->expectNever('save');
    $group2->expectNever('save');

    $collection->save();
  }

  function testSaveWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);

    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $this->assertEqual(sizeof($collection2->getArray()), 0);

    $user->save();
    $collection->save();

    $collection3 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection3->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
  }

  function testSavingOwnerDoesntAffectCollection()
  {
    $group1 = new GroupForTestStub();
    $group1->setTitle('Group1');
    $group2 = new GroupForTestStub();
    $group2->setTitle('Group2');

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);

    $user->save();

    $collection->add($group2);

    //items in memory
    $arr = $collection->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEqual($group1->save_calls, 0);
    $this->assertEqual($group2->save_calls, 0);

    //...and not db yet
    $collection2 = new lmbARManyToManyCollection('groups', $user);
    $this->assertEqual(sizeof($collection2->getArray()), 0);

    $collection->save();

    $collection3 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection3->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());

    //check items not saved twice
    $collection->save();

    $this->assertEqual($group1->save_calls, 1);
    $this->assertEqual($group2->save_calls, 1);

    $collection4 = new lmbARManyToManyCollection('groups', $user);
    $arr = $collection4->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
  }

  function testLoadOnlyProperRecordsWithExistingOwner()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();

    $user1 = $this->_createUserAndSave(array($g1, $g2));

    $g3 = $this->_initGroup();
    $g4 = $this->_initGroup();

    $user2 = $this->_createUserAndSave(array($g3, $g4));

    $collection1 = new lmbARManyToManyCollection('groups', $user1);
    $this->assertEqual($collection1->count(), 2);
    $arr = $collection1->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $g1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $g2->getTitle());

    $collection2 = new lmbARManyToManyCollection('groups', $user2);
    $this->assertEqual($collection2->count(), 2);
    $arr = $collection2->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $g3->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $g4->getTitle());
  }

  function testCountWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEqual($collection->count(), 0);
    $collection->add($group1);
    $collection->add($group2);

    $this->assertEqual($collection->count(), 2);
  }

  function testCountWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = new UserForTest();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEqual($collection->count(), 0);

    $collection->add($group1);
    $collection->add($group2);

    $this->assertEqual($collection->count(), 2);
  }

  function testImplementsCountable()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $this->assertEqual(sizeof($collection), 0);

    $collection->add($group1);
    $collection->add($group2);

    $this->assertEqual(sizeof($collection), 2);
  }

  function testPartiallyImplementsArrayAccess()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave();

    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection[] = $group1;
    $collection[] = $group2;

    $this->assertEqual($collection[0]->getId(), $group1->getId());
    $this->assertEqual($collection[1]->getId(), $group2->getId());
    $this->assertNull($collection[2]);

    $this->assertTrue(isset($collection[0]));
    $this->assertTrue(isset($collection[1]));
    $this->assertFalse(isset($collection[2]));

    //we can't really implement just every php array use case
    $this->assertNull($collection['foo']);
    $this->assertFalse(isset($collection['foo']));
    $collection[3] = 'foo';
    $this->assertNull($collection[3]);
  }

  function testRemoveAllWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->removeAll();

    $user2 = lmbActiveRecord :: findById('UserForTest', $user->getId());

    $collection = new lmbARManyToManyCollection('groups', $user2);
    $this->assertEqual(sizeof($collection->getArray()), 0);
  }

  function testRemoveAllWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->removeAll();

    $this->assertEqual($collection->count(), 0);
  }

  function testPaginateWithNonSavedOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->add($group3);

    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
  }

  function testPaginateWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $group1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
  }

  function testSortWithExistingOwner()
  {
    $group1 = new GroupForTest();
    $group1->setTitle('A-Group');
    $group2 = new GroupForTest();
    $group2->setTitle('B-Group');
    $group3 = new GroupForTest();
    $group3->setTitle('C-Group');

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->sort(array('title' => 'DESC'));

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 3);
    $this->assertEqual($arr[0]->getTitle(), $group3->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $group2->getTitle());
    $this->assertEqual($arr[2]->getTitle(), $group1->getTitle());
  }

  function testSortWithNonSavedOwner()
  {
    $group1 = new GroupForTest();
    $group1->setTitle('A-Group');
    $group2 = new GroupForTest();
    $group2->setTitle('B-Group');
    $group3 = new GroupForTest();
    $group3->setTitle('C-Group');

    $user = $this->_initUser();

    $collection = new lmbARManyToManyCollection('groups', $user);
    $collection->add($group1);
    $collection->add($group2);
    $collection->add($group3);

    $collection->sort(array('title' => 'DESC'));
    $this->assertEqual($collection->at(0)->getTitle(), 'C-Group');
    $this->assertEqual($collection->at(1)->getTitle(), 'B-Group');
    $this->assertEqual($collection->at(2)->getTitle(), 'A-Group');
  }

  function testFindWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $groups = $user->getGroups()->find("group_id=" . $group1->getId());
    $this->assertEqual($groups->count(), 1);
    $this->assertEqual($groups->at(0)->getTitle(), $group1->getTitle());
  }

  function testFindWithNonSavedOwner_TODO()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();
    $user = $this->_initUser(array($g1, $g2));

    try
    {
      $groups = $user->getGroups()->find("id=" . $g1->getId());
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testFindFirstWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));

    $group = $user->getGroups()->findFirst("group_id=" . $group1->getId() . " OR group_id=" . $group2->getId());
    $this->assertEqual($group->getTitle(), $group1->getTitle());
  }

  function testFindFirstWithNonSavedOwner_TODO()
  {
    $g1 = $this->_initGroup();
    $g2 = $this->_initGroup();
    $user = $this->_initUser(array($g1, $g2));

    try
    {
      $group = $user->getGroups()->findFirst("group_id=" . $g1->getId() . " OR group_id=" . $g2->getId());
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testAtWithExistingOwner()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    $collection = new lmbARManyToManyCollection('groups', $user);

    $this->assertEqual($collection->at(0)->getTitle(), $group1->getTitle());
    $this->assertEqual($collection->at(2)->getTitle(), $group3->getTitle());
    $this->assertEqual($collection->at(1)->getTitle(), $group2->getTitle());
  }

  function testSet()
  {
    $group1 = $this->_initGroup();
    $group2 = $this->_initGroup();
    $group3 = $this->_initGroup();

    $user = $this->_createUserAndSave(array($group1, $group2, $group3));
    $collection = new lmbARManyToManyCollection('groups', $user);

    $collection->set(array($group1, $group3));

    $this->assertEqual($collection->count(), 2);
    $this->assertEqual($collection->at(0)->getTitle(), $group1->getTitle());
    $this->assertEqual($collection->at(1)->getTitle(), $group3->getTitle());
  }

  protected function _initUser($groups = array())
  {
    $user = new UserForTest();
    $user->setFirstName('User' . mt_rand());

    if(sizeof($groups))
    {
      foreach($groups as $group)
        $user->getGroups()->add($group);
    }

    return $user;
  }

  protected function _createUserAndSave($groups = array())
  {
    $user = $this->_initUser($groups);
    $user->save();
    return $user;
  }

  protected function _initGroup()
  {
    $group = new GroupForTest();
    $group->setTitle('Group' . mt_rand());
    return $group;
  }

}

?>
