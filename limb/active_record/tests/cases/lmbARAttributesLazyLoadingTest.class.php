<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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

