<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class LazyTestOneTableObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
  protected $_lazy_attributes = array('annotation', 'content');
}

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

