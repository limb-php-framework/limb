<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class GroupsForTestCollectionStub extends lmbARManyToManyCollection{}

class UserForTestWithCustomCollection extends lmbActiveRecord
{
  protected $_db_table_name = 'user_for_test';

  protected $_has_many_to_many = array('groups' => array('field' => 'user_id',
                                                         'foreign_field' => 'group_id',
                                                         'table' => 'user_for_test2group_for_test',
                                                         'class' => 'GroupForTest',
                                                         'collection' => 'GroupsForTestCollectionStub'));
}

class lmbARManyToManyRelationsTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('user_for_test', 'group_for_test', 'user_for_test2group_for_test', 'test_one_table_object'); 

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
    $user = $this->creator->initUser();

    $group1 = $this->creator->initGroup();
    $group2 = $this->creator->initGroup();

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
  
  function testSetRelation()
  {
    $user1 = $this->creator->initUser();
    $user2 = $this->creator->initUser();
      
    $group1 = $this->creator->initGroup();
    $group2 = $this->creator->initGroup();
      
    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
    $user2->addToGroups($group1);
    $user2->addToGroups($group2);
      
    $user1->save();
    $user2->save();
    $this->assertEqual($user1->getGroups()->count(), 2);
    $this->assertEqual($user2->getGroups()->count(), 2);
    
    $user1->getGroups()->set(array($group1));
    $user1->save();
    $user2->save();
     
    $this->assertEqual($user1->getGroups()->count(), 1);
    $this->assertEqual($user2->getGroups()->count(), 2);
  }
  
  function testLoadShouldNotMixTables()
  {
    $user1 = $this->creator->initUser();
    $user2 = $this->creator->initUser();

    $group1 = $this->creator->initGroup();
    $group2 = $this->creator->initGroup();
    
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

  function testFetch_WithRelatedObjectsUsing_WithMethod()
  {
    $linked_object1 = $this->creator->createOneTableObject();
    $linked_object2 = $this->creator->createOneTableObject();
    
    $user1 = $this->creator->createUser($linked_object1);
    $user2 = $this->creator->createUser($linked_object2);
    
    $group = $this->creator->createGroup();

    $group->setUsers(array($user1, $user2));    

    $group2 = lmbActiveRecord :: findById('GroupForTest', $group->getId());
    $arr = $group2->getUsers()->join('linked_object')->getArray();

    //make sure we really eager fetching
    $this->db->delete('test_one_table_object');

    $this->assertEqual($arr[0]->getFirstName(), $user1->getFirstName());
    $this->assertEqual($arr[1]->getFirstName(), $user2->getFirstName());
  }

  function testSetingCollectionDirectlyCallsAddToMethod()
  {
    $user = $this->creator->initUser();
    
    $g1 = $this->creator->initGroup();
    $g2 = $this->creator->initGroup();

    $user->setGroups(array($g1, $g2));
    $arr = $user->getGroups()->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $g1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $g2->getTitle());
  }

  function testSetFlushesPreviousCollection()
  {
    $user = $this->creator->initUser();
    
    $g1 = $this->creator->initGroup();
    $g2 = $this->creator->initGroup();
    
    $user->addToGroups($g1);
    $user->addToGroups($g2);

    $user->setGroups(array($g1));
    $groups = $user->getGroups()->getArray();
    $this->assertEqual($groups[0]->getTitle(), $g1->getTitle());
    $this->assertEqual(sizeof($groups), 1);
  }

  function testUpdateRelations()
  {
    $user = $this->creator->initUser();
    
    $group1 = $this->creator->initGroup();
    $group2 = $this->creator->initGroup();
    
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
    $user1 = $this->creator->initUser();
    $user2 = $this->creator->initUser();

    $group1 = $this->creator->initGroup();
    $group2 = $this->creator->initGroup();
    
    $user1->addToGroups($group1);
    $user1->addToGroups($group2);
    $user1->save();

    $user2->addToGroups($group1);
    $user2->addToGroups($group2);
    $user2->save();

    $user3 = lmbActiveRecord :: findById('UserForTest', $user1->getId());
    $user3->destroy();

    $this->assertEqual($this->db->count('user_for_test2group_for_test'), 2);

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

  function testErrorListIsSharedWithCollection()
  {
    $user = $this->creator->initUser();

    $group = new GroupForTest();

    $validator = new lmbValidator();
    $validator->addRequiredRule('title');
    $group->setValidator($validator);

    $user->addToGroups($group);

    $error_list = new lmbErrorList();
    $this->assertFalse($user->trySave($error_list));
  }

  function testManyToManyRelationWithCriteria()
  {
    $user = $this->creator->initUser();

    $g1 = $this->creator->createGroup('foo');
    $g2 = $this->creator->createGroup('bar');
    $g3 = $this->creator->createGroup('condition');
    $this->assertEqual('condition', $g3->getTitle());

    $user->setGroups(array($g1, $g2,$g3));
    $user->save();
    $user = new UserForTest($user->id);
    $arr = $user->getCgroups()->getArray();
    $this->assertIsA($arr[0], 'GroupForTest');
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]->getTitle(), $g3->getTitle());
  }

}


