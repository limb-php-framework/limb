<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/controller/AdminObjectController.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/web_app/src/tests/lmbWebApplicationSandbox.class.php');

class ObjectForTesting extends lmbActiveRecord
{
  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('field');
    return $validator;
  }
}

class TestAdminObjectController extends AdminObjectController
{
  protected $_object_class_name = 'ObjectForTesting';
  protected $in_popup = false;

  protected function _onBeforeSave() { $this->response->append('onBeforeSave|'); }
  protected function _onAfterSave() { $this->response->append('onAfterSave|'); }

  protected function _onBeforeValidate() { $this->response->append('onBeforeValidate|'); }
  protected function _onAfterValidate() { $this->response->append('onAfterValidate|'); }

  protected function _onBeforeCreate() { $this->response->append('onBeforeCreate|'); }
  protected function _onAfterCreate() { $this->response->append('onAfterCreate|'); }

  protected function _onBeforeEdit() { $this->response->append('onBeforeEdit|'); }
  protected function _onAfterEdit() { $this->response->append('onAfterEdit|'); }

  protected function _onBeforeDelete() { $this->response->append('onBeforeDelete|'); }
  protected function _onAfterDelete() { $this->response->append('onAfterDelete|'); }

  protected function _initCreateForm() { $this->response->append('initCreateForm|'); }
  protected function _initEditForm() { $this->response->append('initEditForm|'); }
}

class AdminObjectControllerTest extends UnitTestCase
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

  function testEventsOnPerformCreateActionFirstTime()
  {
    $request = new lmbHttpRequest('/test_admin_object/create');

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEqual($response->getResponseString(), 'initCreateForm|');
  }

  function testEventsOnPerformCreateActionWithPost()
  {
    $request = new lmbHttpRequest('/test_admin_object/create', array(), array('field' => 'test'));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|onBeforeCreate|onBeforeSave|onAfterSave|onAfterCreate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformCreateActionWithPostNotValid()
  {
    $request = new lmbHttpRequest('/test_admin_object/create', array(), array('field' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionFirstTime()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId());

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEqual($response->getResponseString(), 'initEditForm|');
  }

  function testEventsOnPerformEditActionWithPostNotValid()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId(), array(), array('id' => $object->getId(), 'field' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionWithPost()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/edit/' . $object->getId(), array(), array('id' => $object->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|onBeforeEdit|onBeforeSave|onAfterSave|onAfterEdit|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformDeleteAction()
  {
    $object = new ObjectForTesting();
    $object->setField('test');
    $object->save();

    $request = new lmbHttpRequest('/test_admin_object/delete/' . $object->getId(), array(), array('id' => $object->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeDelete|onAfterDelete|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }
}

?>