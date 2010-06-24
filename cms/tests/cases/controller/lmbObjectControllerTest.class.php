<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/controller/lmbObjectController.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/web_app/src/tests/lmbWebApplicationSandbox.class.php');

class ObjectForTesting extends lmbActiveRecord
{ 
  protected $_db_table_name = 'cms_object_for_testing';
}

class TestObjectController extends lmbObjectController
{
  protected $_object_class_name = 'ObjectForTesting';
  protected $in_popup = false;
}

class lmbObjectControllerTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    lmbActiveRecord :: delete('ObjectForTesting');
  }

  function testDoDisplay()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();
    
    $request = new lmbHttpRequest('/test_object/', array(), array());    
    lmbToolkit::instance()->setRequest($request);

    $controller = new TestObjectController();
    $controller->doDisplay();
    
    if($this->assertEqual(count($controller->items), 1))
      if($this->assertIsA($controller->items[0], 'ObjectForTesting'))
        $this->assertEqual($controller->items[0]->getId(), $object->getId());
  }
  
  function testDoItem()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();
    
    $request = new lmbHttpRequest('/test_object/item/' . $object->getId(), array(), array('id' => $object->getId()));
    lmbToolkit::instance()->setRequest($request);

    $controller = new TestObjectController();
    $controller->doItem();
        
    if($this->assertIsA($controller->item, 'ObjectForTesting'))
        $this->assertEqual($controller->item->getId(), $object->getId());
  }
}


