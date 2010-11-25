<?php
lmb_require('limb/core/src/lmbSerializable.class.php');

class lmbARSerializationTest extends lmbARBaseTestCase
{
  protected static function serializeAndUnserialize($obj)
  {
    $serilized = unserialize(serialize(new lmbSerializable($obj)));
    return $serilized->getSubject();
  }
  
  function testSerializeInMemorySingleTableObject()
  {
    $obj = new TestOneTableObject();
    $obj->setAnnotation('foo');
    
    $recreated_obj = self::serializeAndUnserialize($obj);
    
    $this->assertEqual('foo', $recreated_obj->getAnnotation());
  }
  
  function testSaveAndSerializeSingleTableObject()
  {
    $obj = new TestOneTableObject();
    $obj->setAnnotation('foo');
    $id = $obj->save();
    
    $recreated_obj = self::serializeAndUnserialize($obj);
    
    $this->assertFalse($recreated_obj->isNew());
    $this->assertEqual($id, $recreated_obj->getId());
    $this->assertEqual('foo', $recreated_obj->getAnnotation());
  }
  
  function testSerializeAndSaveSingleTableObject()
  {
    $obj = new TestOneTableObject();
    $obj->setAnnotation('foo');
    
    $recreated_obj = self::serializeAndUnserialize($obj);
    
    $id = $recreated_obj->save();
    $this->assertFalse($recreated_obj->isNew());
    $this->assertEqual($id, $recreated_obj->getId());
    $this->assertEqual('foo', $recreated_obj->getAnnotation());
  }
  
  function testSerializeSaveAndSerializeAgainSingleTableObject()
  {
    $obj = new TestOneTableObject();
    $obj->setAnnotation('foo');
    
    $recreated_obj = self::serializeAndUnserialize($obj);
    $id = $recreated_obj->save();
    $recreated_obj2 = self::serializeAndUnserialize($recreated_obj);
    
    $this->assertFalse($recreated_obj2->isNew());
    $this->assertEqual($id, $recreated_obj2->getId());
    $this->assertEqual('foo', $recreated_obj2->getAnnotation());
  }
  
  function testSaveSerializeAndTouchFieldInSingleTableObject()
  {
    $obj = new TestOneTableObject();
    $obj->setAnnotation('foo');
    $id = $obj->save();
    
    $recreated_obj = self::serializeAndUnserialize($obj);
    $recreated_obj->setAnnotation('bar');
    $recreated_obj2 = self::serializeAndUnserialize($recreated_obj);
    
    $this->assertEqual('bar', $recreated_obj2->getAnnotation());
    
    $recreated_obj2->save();
    $recreated_obj3 = self::serializeAndUnserialize($recreated_obj2);
    
    $this->assertEqual('bar', $recreated_obj2->getAnnotation());
    $saved_obj = lmbActiveRecord::findById('TestOneTableObject', $id, true);
    $this->assertEqual($saved_obj->getAnnotation(), $recreated_obj3->getAnnotation());
  }
  
  function testSerializeInMemoryWithOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject();
    $linked_obj->setAnnotation('foo');
    $user = new UserForTest();
    $user->setLinkedObject($linked_obj);
    $user->setFirstName('bar');
    
    $recreated_user = self::serializeAndUnserialize($user);
    
    $recreated_linked_obj = $recreated_user->getLinkedObject();
    $this->assertTrue(is_object($recreated_linked_obj));
    $this->assertEqual(get_class($recreated_linked_obj), get_class($linked_obj));
    $this->assertEqual('foo', $recreated_linked_obj->getAnnotation());
  }
  
  function testSerializeAndSaveWithOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject();
    $linked_obj->setAnnotation('foo');
    $user = new UserForTest();
    $user->setLinkedObject($linked_obj);
    $user->setFirstName('bar');
    
    $recreated_user = self::serializeAndUnserialize($user);    
    $recreated_user->save();

    $this->assertTrue((bool)$recreated_user->getLinkedObject()->getId());
    $this->assertEqual('foo', $recreated_user->getLinkedObject()->getAnnotation());
  }
  
  function testSaveAndSerializeWithOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject();
    $linked_obj->setAnnotation('foo');
    $user = new UserForTest();
    $user->setLinkedObject($linked_obj);
    $user->setFirstName('bar');
    
    $user->save();
    $recreated_user = self::serializeAndUnserialize($user);

    $this->assertTrue((bool)$recreated_user->getLinkedObject()->getId());
    $this->assertEqual('foo', $recreated_user->getLinkedObject()->getAnnotation());
  }
  
  function testSaveSerializeAndTouchFieldWithOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject(array('annotation' => 'foo'));
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setLinkedObject($linked_obj);
    $id = $user->save();
    
    $recreated_user = self::serializeAndUnserialize($user);
    $recreated_user->getLinkedObject()->setAnnotation('bar');
    $recreated_user2 = self::serializeAndUnserialize($recreated_user);

    $this->assertEqual('bar', $recreated_user2->getLinkedObject()->getAnnotation());
    
    $id2 = $recreated_user2->save();
    $recreated_user3 = self::serializeAndUnserialize($recreated_user2);
    
    $this->assertEqual($id, $id2);
    $this->assertEqual('bar', $recreated_user3->getLinkedObject()->getAnnotation());
  }
  
  function testFirstSerializeThenReplaceObjectInOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject(array('annotation' => 'foo'));
    $linked_obj2 = new TestOneTableObject(array('annotation' => 'zoo'));
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setLinkedObject($linked_obj);
    
    $recreated_user = self::serializeAndUnserialize($user);
    $id = $recreated_user->save();
    
    $recreated_user2 = self::serializeAndUnserialize($recreated_user);
    $recreated_user->setLinkedObject($linked_obj2);
    $recreated_user2 = self::serializeAndUnserialize($recreated_user);

    $this->assertEqual('zoo', $recreated_user2->getLinkedObject()->getAnnotation());
    
    $recreated_user2->save();
    $recreated_user3 = self::serializeAndUnserialize($recreated_user2);
    
    $this->assertEqual('zoo', $recreated_user3->getLinkedObject()->getAnnotation());
  }
  
  function testFirstSerializeThenRemoveObjectInOneToOneRelation()
  {
    $linked_obj = new TestOneTableObject(array('annotation' => 'foo'));
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setLinkedObject($linked_obj);
    
    $recreated_user = self::serializeAndUnserialize($user);
    $id = $recreated_user->save();
    
    $recreated_user2 = self::serializeAndUnserialize($recreated_user);
    $recreated_user->setLinkedObject(null);
    $this->assertNull($recreated_user->getLinkedObject());
    
    $recreated_user2 = self::serializeAndUnserialize($recreated_user);
    $this->assertNull($recreated_user2->getLinkedObject());
    
    $recreated_user2->save();
    $recreated_user3 = self::serializeAndUnserialize($recreated_user2);    
    $this->assertNull($recreated_user3->getLinkedObject());
  }
  
  function testSerializeInMemoryWithManyToOneRelation()
  {
    $course = new CourseForTest(array('title' => 'foo'));    
    $l1 = new LectureForTest(array('title' => 'foo1'));
    $l2 = new LectureForTest(array('title' => 'foo2'));
    $course->setLectures(array($l1, $l2));
    
    $recreated_course = self::serializeAndUnserialize($course);
    
    $lectures = $recreated_course->getLectures();
    if (2 == count($lectures))
    {
      $this->assertTrue($l1->getId(), $lectures[0]->getId());
      $this->assertEqual('foo1', $lectures[0]->getTitle());
    }
    else $this->fail();
  }
  
  function testSerializeAndSaveWithManyToOneRelation()
  {
    $course = new CourseForTest(array('title' => 'foo'));    
    $l1 = new LectureForTest(array('title' => 'foo1'));
    $l2 = new LectureForTest(array('title' => 'foo2'));
    $course->setLectures(array($l1, $l2));
    
    $recreated_course = self::serializeAndUnserialize($course);
    $recreated_course->save();
    $recreated_course = self::serializeAndUnserialize($recreated_course);
    
    $lectures = $recreated_course->getLectures();
    if (2 == count($lectures))
    {
      $this->assertTrue((bool)$lectures[1]->getId());
      $this->assertEqual('foo2', $lectures[1]->getTitle());
    }
    else $this->fail();
  }
  
  function testModifyObjectsOfManyToOneRelationInMemory()
  {
    $course = new CourseForTest(array('title' => 'foo'));    
    $l1 = new LectureForTest(array('title' => 'foo1'));
    $l2 = new LectureForTest(array('title' => 'foo2'));
    $l3 = new LectureForTest(array('title' => 'foo3'));
    $course->setLectures(array($l1, $l2));
    
    $recreated_course = self::serializeAndUnserialize($course);
    $this->assertEqual(2, count($recreated_course->getLectures()));
    
    // how to do this?
    //$recreated_course->remove($l2); 
    //$recreated_course = self::serializeAndUnserialize($recreated_course);
    //$this->assertEqual(1, count($recreated_course->getLectures()));
    
    $recreated_course->addToLectures($l3);
    $recreated_course = self::serializeAndUnserialize($recreated_course);
    $lectures = $recreated_course->getLectures();
    if (3 == count($lectures))
    {
      $this->assertEqual(3, count($lectures));
      $this->assertEqual('foo3', $lectures[2]->getTitle());
    }
    else $this->fail();
  }
  
  function testSerializeInMemoryWithManyToManyRelation()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));    
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setGroups(array($g1, $g2));
    
    $recreated_user = self::serializeAndUnserialize($user);
    
    $recreated_groups = $recreated_user->getGroups();
    $this->assertEqual(2, count($recreated_groups));
    $this->assertEqual($g1->getId(), $recreated_groups[0]->getId());
    $this->assertEqual('foo1', $recreated_groups[0]->getTitle());
  }
  
  function testSerializeInMemoryWithManyToManyRelationUsingAddToInterface()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));    
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->addToGroups($g1);
    $user->addToGroups($g2);
    
    $recreated_user = self::serializeAndUnserialize($user);
    
    $recreated_groups = $recreated_user->getGroups();
    $this->assertEqual(2, count($recreated_groups));
    $this->assertEqual($g1->getId(), $recreated_groups[0]->getId());
    $this->assertEqual('foo1', $recreated_groups[0]->getTitle());
  }
  
  function testSerializeAndSaveWithManyToManyRelation()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));    
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setGroups(array($g1, $g2));
    
    $recreated_user = self::serializeAndUnserialize($user);
    $recreated_user->save();
    
    $recreated_groups = $recreated_user->getGroups();
    $this->assertEqual(2, count($recreated_groups));
    $this->assertTrue((bool)$recreated_groups[0]->getId());
    $this->assertEqual('foo1', $recreated_groups[0]->getTitle());
  }
  
  function testSaveAndSerializeWithManyToManyRelation()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));    
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setGroups(array($g1, $g2));
    
    $user->save();
    $recreated_user = self::serializeAndUnserialize($user);
    
    $recreated_groups = $recreated_user->getGroups();
    $this->assertEqual(2, count($recreated_groups));
    $this->assertEqual($g1->getId(), $recreated_groups[0]->getId());
    $this->assertEqual('foo1', $recreated_groups[0]->getTitle());
  }
  
  function testSerializeSaveAndTouchWithManyToManyRelation()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));    
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setGroups(array($g1, $g2));
    
    $recreated_user = self::serializeAndUnserialize($user);
    $recreated_user->save();
    
    $groups = $recreated_user->getGroups();
    $groups[0]->setTitle('zoo1');
    $groups[1]->setTitle('zoo2');
    $recreated_user->save();
    
    $recreated_groups2 = $recreated_user->getGroups();
    $this->assertEqual(2, count($recreated_groups2));
    $this->assertEqual('zoo1', $recreated_groups2[0]->getTitle());
    
    $recreated_user3 = self::serializeAndUnserialize($recreated_user);
    $recreated_groups3 = $recreated_user3->getGroups();
    $this->assertEqual(2, count($recreated_groups3));
    $this->assertEqual('zoo1', $recreated_groups3[0]->getTitle());
  }
 
  function testModifyManyToManyRelationCollection()
  {
    $g1 = new GroupForTest(array('title' => 'foo1'));
    $g2 = new GroupForTest(array('title' => 'foo2'));
    $g3 = new GroupForTest(array('title' => 'foo3'));
    $user = new UserForTest(array('first_name' => 'bar'));
    $user->setGroups(array($g1, $g2));
    
    $recreated_user = self::serializeAndUnserialize($user);
    $recreated_user->save();
    $recreated_user = self::serializeAndUnserialize($recreated_user);
    $this->assertEqual(2, count($recreated_user->getGroups()));
    
    $groups = $recreated_user->getGroups();
    $groups->remove($g2);
    $recreated_user = self::serializeAndUnserialize($recreated_user);
    $this->assertEqual(1, count($recreated_user->getGroups()));
    
    $recreated_user->addToGroups($g3);
    $recreated_user = self::serializeAndUnserialize($recreated_user);
    $groups = $recreated_user->getGroups();
    $this->assertEqual(2, count($groups));
    $this->assertEqual('foo3', $groups[1]->getTitle());
  }
  
}