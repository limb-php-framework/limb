<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordManyToManyRelationsTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class GroupForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'group_for_test';

  protected $_has_many_to_many = array('users' => array('field' => 'group_id',
                                                        'foreign_field' => 'user_id',
                                                        'table' => 'user2group_for_test',
                                                        'class' => 'UserForTest'));
}

class UserForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'user_for_test';

  protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                         'foreign_field' => 'group_id',
                                                         'table' => 'user2group_for_test',
                                                         'class' => 'GroupForTest'));
}

class GroupsForTestCollectionStub extends lmbARManyToManyCollection{}

class UserForTestWithCustomCollection extends lmbActiveRecord
{
  protected $_db_table_name = 'user_for_test';

  protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                         'foreign_field' => 'group_id',
                                                         'table' => 'user2group_for_test',
                                                         'class' => 'GroupForTest',
                                                         'collection' => 'GroupsForTestCollectionStub'));
}

class lmbActiveRecordManyToManyRelationsTest extends UnitTestCase
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

  function testMapPropertyToField()
  {
    $group = new GroupForTest();
    $this->assertEqual('users', $group->mapFieldToProperty('group_id'));
    $this->assertNull($group->mapFieldToProperty('blah'));
  }

  function testNewObjectReturnsEmptyCollection()
  {
    $user = new UserForTest();
    $groups = $user->getGroups();
    $groups->rewind();
    $this->assertFalse($groups->valid());
  }

  function testAddFromOneSideOfRelation()
  {
    $user = new UserForTest();
    $user->setFirstName('Bob');

    $group1 = new GroupForTest();
    $group1->setTitle('vp1');

    $group2 = new GroupForTest();
    $group2->setTitle('vp2');

    $user->addToGroups($group1);
    $user->addToGroups($group2);
    $user->save();

    $user2 = lmbActiveRecord :: findById('UserForTest', $user->getId());
    $rs = $user2->getGroups();

    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEqual($rs->current()->getTitle(), $group1->getTitle());
    $this->assertEqual($rs->current()->getId(), $group1->getId());
    $rs->next();
    $this->assertEqual($rs->current()->getTitle(), $group2->getTitle());
    $this->assertEqual($rs->current()->getId(), $group2->getId());
  }

  function testLoadShouldNotMixTables()
  {
    $user1 = new UserForTest();
    $user1->setFirstName('Bob');

    $user2 = new UserForTest();
    $user2->setFirstName('Joe');

    $group1 = new GroupForTest();
    $group1->setTitle('vp1');

    $group2 = new GroupForTest();
    $group2->setTitle('vp2');

    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
    $user1->save();

    $user2->addToGroups($group1);
    $user2->addToGroups($group2);
    $user2->save();

    $user3 = lmbActiveRecord :: findById('UserForTest', $user2->getId());
    $rs = $user3->getGroups();

    $rs->rewind();
    $this->assertTrue($rs->valid());
    $this->assertEqual($rs->current()->getTitle(), $group1->getTitle());
    $this->assertEqual($rs->current()->getId(), $group1->getId());
    $rs->next();
    $this->assertEqual($rs->current()->getTitle(), $group2->getTitle());
    $this->assertEqual($rs->current()->getId(), $group2->getId());
  }

  function testSetingCollectionDirectlyCallsAddToMethod()
  {
    $user = new UserForTest();
    $user->setFirstName('Bob');

    $g1 = new GroupForTest();
    $g1->setTitle('vp1');
    $g2 = new GroupForTest();
    $g2->setTitle('vp2');

    $user->setGroups(array($g1, $g2));
    $this->assertEqual($user->getGroups()->getArray(), array($g1, $g2));
  }

  function testSetFlushesPreviousCollection()
  {
    $user = new UserForTest();
    $user->setFirstName('Bob');

    $g1 = new GroupForTest();
    $g1->setTitle('vp1');
    $g2 = new GroupForTest();
    $g2->setTitle('vp2');

    $user->addToGroups($g1);
    $user->addToGroups($g2);

    $user->setGroups(array($g1));
    $groups = $user->getGroups()->getArray();
    $this->assertEqual($groups[0]->getTitle(), $g1->getTitle());
    $this->assertEqual(sizeof($groups), 1);
  }

  function testUpdateRelations()
  {
    $user = new UserForTest();
    $user->setFirstName('Bob');

    $group1 = new GroupForTest();
    $group1->setTitle('vp1');

    $group2 = new GroupForTest();
    $group2->setTitle('vp2');

    $user->addToGroups($group1);
    $user->addToGroups($group2);
    $user->save();

    $user2 = lmbActiveRecord :: findById('UserForTest', $user->getId());
    $user2->setGroups(array($group2));
    $user2->save();

    $user3 = lmbActiveRecord :: findById('UserForTest', $user->getId());
    $groups = $user3->getGroups();

    $this->assertEqual($groups->at(0)->getTitle(), $group2->getTitle());
    $this->assertEqual($groups->count(), 1);
  }

  function testDeleteAlsoRemovesManyToManyRecords()
  {
    $user1 = new UserForTest();
    $user1->setFirstName('Bob');

    $user2 = new UserForTest();
    $user2->setFirstName('Bob');

    $group1 = new GroupForTest();
    $group1->setTitle('vp1');

    $group2 = new GroupForTest();
    $group2->setTitle('vp2');

    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
    $user1->save();

    $user2->addToGroups($group1);
    $user2->addToGroups($group2);
    $user2->save();

    $user3 = lmbActiveRecord :: findById('UserForTest', $user1->getId());
    $user3->destroy();

    $this->assertEqual($this->db->count('user2group_for_test'), 2);

    $user4 = lmbActiveRecord :: findById('UserForTest', $user2->getId());
    $groups = $user4->getGroups();
    $this->assertEqual($groups->at(0)->getTitle(), $group1->getTitle());
    $this->assertEqual($groups->at(1)->getTitle(), $group2->getTitle());
    $this->assertEqual($groups->count(), 2);
  }

  function testUseCustomCollection()
  {
    $user = new UserForTestWithCustomCollection();
    $this->assertTrue($user->getGroups() instanceof GroupsForTestCollectionStub);
  }
}

?>
