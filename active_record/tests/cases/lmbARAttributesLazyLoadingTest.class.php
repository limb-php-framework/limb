<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbARAttributesLazyLoadingTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object'); 
  
  function testLazyFind()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $object2 = lmbActiveRecord :: findById('LazyTestOneTableObject', $object->getId());

    $this->_checkLazyness($object2, $annotation, $content);
  }

  function testLazyLoadById()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');

    $object2 = new LazyTestOneTableObject();
    $object2->loadById($object->getId());

    $this->_checkLazyness($object2, $annotation, $content);
  }

  function testForceToLoadAllLazyAttributes()
  {
    $object1 = $this->_createActiveRecord('Some annotation', 'Some content');
    $object2 = $this->_createActiveRecord('Some other annotation', 'Some other content');
    
    $query = lmbARQuery :: create('LazyTestOneTableObject', $params = array('with_lazy_attributes' => ''));
    $arr = $query->fetch()->getArray();
    $this->assertTrue(array_key_exists('annotation', $arr[0]->exportRaw()));
    $this->assertTrue(array_key_exists('content', $arr[0]->exportRaw()));
    $this->assertTrue(array_key_exists('annotation', $arr[1]->exportRaw()));
    $this->assertTrue(array_key_exists('content', $arr[1]->exportRaw()));
  }

  function testForceToLoadSomeLazyAttributes()
  {
    $object1 = $this->_createActiveRecord('Some annotation', 'Some content');
    $object2 = $this->_createActiveRecord('Some other annotation', 'Some other content');
    
    $query = lmbARQuery :: create('LazyTestOneTableObject', $params = array('with_lazy_attributes' => array('annotation')));
    $arr = $query->fetch()->getArray();
    $this->assertTrue(array_key_exists('annotation', $arr[0]->exportRaw()));
    $this->assertFalse(array_key_exists('content', $arr[0]->exportRaw()));
    $this->assertTrue(array_key_exists('annotation', $arr[1]->exportRaw()));
    $this->assertFalse(array_key_exists('content', $arr[1]->exportRaw()));
  }
  
  function testLazyWorksOkForEagerJoin_OneToOneRelations()
  {
    $person = new PersonForLazyAttributesTest();
    $person->setName('Some name');
    
    $lazy_object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $person->set('lazy_object', $lazy_object);

    $person->save();
    
    $person_loaded = lmbActiveRecord :: findOne('PersonForLazyAttributesTest', 
                                                array('criteria' => 'person_for_test.id = ' . $person->getId(),
                                                      'join' => 'lazy_object'));
    
    $lazy_object2 = $person_loaded->getLazyObject(); 
    $this->_checkLazyness($lazy_object2, $annotation, $content);
  }

  function testForceToLoadAllLazyAttributes_ForEagerJoin_OneToOneRelations()
  {
    $person = new PersonForLazyAttributesTest();
    $person->setName('Some name');
    
    $lazy_object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $person->set('lazy_object', $lazy_object);

    $person->save();
    
    $person_loaded = lmbActiveRecord :: findOne('PersonForLazyAttributesTest', 
                                                array('criteria' => 'person_for_test.id = ' . $person->getId(),
                                                      'join' => array('lazy_object' => array('with_lazy_attributes' => ''))));
    
    $lazy_object2 = $person_loaded->getLazyObject(); 
    $this->assertTrue(array_key_exists('annotation', $lazy_object2->exportRaw()));
    $this->assertTrue(array_key_exists('content', $lazy_object2->exportRaw()));
 }
  
  function testLazyWorksOkForEagerJoin_ForParentObject_OneToOneRelations()
  {
    $person = new PersonForLazyAttributesTest();
    $person->setName($name = 'Some name');
    
    $lazy_object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $person->set('lazy_object', $lazy_object);

    $person->save();
    
    $person_loaded = lmbActiveRecord :: findOne('PersonForLazyAttributesTest', 
                                                array('criteria' => 'person_for_test.id = ' . $person->getId(),
                                                      'join' => 'lazy_object'));
    $this->assertFalse(array_key_exists('name', $person_loaded->exportRaw()));
  }

  function testLazyWorksOkForEagerAttach_OneToOneRelations()
  {
    $person = new PersonForLazyAttributesTest();
    $person->setName('Some name');
    
    $lazy_object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $person->set('lazy_object', $lazy_object);

    $person->save();
    
    $person_loaded = lmbActiveRecord :: findOne('PersonForLazyAttributesTest', 
                                                array('criteria' => 'person_for_test.id = ' . $person->getId(),
                                                      'attach' => 'lazy_object'));
    
    $lazy_object2 = $person_loaded->getLazyObject(); 
    $this->_checkLazyness($lazy_object2, $annotation, $content);
  }
  
  function testExportIsNotLazy()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $object2 = lmbActiveRecord :: findById('LazyTestOneTableObject', $object->getId());
    $exported = $object2->export();
    $this->assertEqual($exported['annotation'], $annotation);
    $this->assertEqual($exported['content'], $content);
  }

  function testCustomLazyFieldsInFindById()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation($annotation = "Annotation");
    $object->setContent($content = "Content");
    $object->save();

    $object2 = lmbActiveRecord :: findById('TestOneTableObject', array('id' => $object->getId(), 'fields' => array('annotation')));
    $fields = $object2->exportRaw();
    //checking which props were actually loaded
    $this->assertEqual($fields, array('id' => $object->getId(), 'annotation' => $annotation));

    //lazy loading in action
    $this->assertEqual($object2->getAnnotation(), $annotation);
    $this->assertEqual($object2->getContent(), $content);
  }

  function testCustomLazyFieldsInFind()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation($annotation = "Annotation");
    $object->setContent($content = "Content");
    $object->save();

    $rs = lmbActiveRecord :: find('TestOneTableObject', array('fields' => array('annotation')));
    $object2 = $rs->at(0);
    $fields = $object2->exportRaw();
    //checking which props were actually loaded
    $this->assertEqual($fields, array('id' => $object->getId(), 'annotation' => $annotation));

    //lazy loading in action
    $this->assertEqual($object2->getAnnotation(), $annotation);
    $this->assertEqual($object2->getContent(), $content);
  }

  function testCustomLazyFieldsInFindFirst()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation($annotation = "Annotation");
    $object->setContent($content = "Content");
    $object->save();

    $object2 = lmbActiveRecord :: findFirst('TestOneTableObject', array('fields' => array('annotation')));
    $fields = $object2->exportRaw();
    //checking which props were actually loaded
    $this->assertEqual($fields, array('id' => $object->getId(), 'annotation' => $annotation));

    //lazy loading in action
    $this->assertEqual($object2->getAnnotation(), $annotation);
    $this->assertEqual($object2->getContent(), $content);
  }

  function testLazyFieldsInOneToManyRelations()
  {
    $course = $this->creator->createCourse();

    $l1 = $this->creator->createLecture($course);
    $l1->setTitle('Lecture1');
    $l1->save();
    $l2 = $this->creator->createLecture($course);
    $l2->setTitle('Lecture2');
    $l2->save();

    //all fields are lazy
    $lectures = $course->getLectures()->find(array('fields' => array()));
    $this->assertEqual(sizeof($lectures), 2);

    $fields1 = $lectures[0]->exportRaw();
    $this->assertFalse(isset($fields1['title']));
    //lazy loading kicks in
    $this->assertEqual($lectures[0]->getTitle(), 'Lecture1');

    $fields2 = $lectures[1]->exportRaw();
    $this->assertFalse(isset($fields2['title']));
    //lazy loading kicks in
    $this->assertEqual($lectures[1]->getTitle(), 'Lecture2');
  }

  function testLazyFieldsInManyToManyRelations()
  {
    $group = $this->creator->createGroup();

    $u1 = $this->creator->createUser();
    $u1->setFirstName("bob1");
    $u1->save();
    $u2 = $this->creator->createUser();
    $u2->setFirstName("bob2");
    $u2->save();

    $group->getUsers()->add($u1);
    $group->getUsers()->add($u2);

    //all fields are lazy
    $users = $group->getUsers()->find(array('fields' => array()));
    $this->assertEqual(sizeof($users), 2);

    $fields1 = $users[0]->exportRaw();
    $this->assertFalse(isset($fields1['title']));
    //lazy loading kicks in
    $this->assertEqual($users[0]->getFirstName(), 'bob1');

    $fields2 = $users[1]->exportRaw();
    $this->assertFalse(isset($fields2['title']));
    //lazy loading kicks in
    $this->assertEqual($users[1]->getFirstName(), 'bob2');
  }

  protected function _checkLazyness($object, $annotation, $content)
  {
    $this->assertTrue($object->has('news_date'));

    $this->assertFalse(array_key_exists('annotation', $object->exportRaw()));
    $this->assertTrue($object->has('annotation'));
    $this->assertEqual($object->getAnnotation(), $annotation);
    $this->assertTrue($object->has('annotation'));
    $this->assertTrue(array_key_exists('annotation', $object->exportRaw()));

    $this->assertFalse(array_key_exists('content', $object->exportRaw()));
    $this->assertTrue($object->has('content'));
    $this->assertEqual($object->getContent(), $content);
    $this->assertTrue($object->has('content'));
    $this->assertTrue(array_key_exists('content', $object->exportRaw()));
  }

  protected function _createActiveRecord($annotation, $content)
  {
    $object = new LazyTestOneTableObject();
    $object->setAnnotation($annotation);
    $object->setContent($content);
    $object->save();
    return $object;
  }
}

