<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/net/src/lmbHttpRequest.class.php');
lmb_require('limb/web_app/src/tests/lmbWebApplicationSandbox.class.php');

class NodeObjectForTesting extends lmbActiveRecord
{
  protected $_db_table_name = 'object_for_testing';

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('field');
    return $validator;
  }
}

class TestAdminNodeWithObjectController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'NodeObjectForTesting';
  protected $_controller_name = 'node';
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

class AdminNodeWithObjectControllerTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->_cleanUp();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    try
    {
      lmbActiveRecord :: delete('lmbCmsNode');
      lmbActiveRecord :: delete('ObjectForTesting');
    }
    catch(lmbException $e) {}
  }

  function testEventsOnPerformCreateActionFirstTime()
  {
    $request = new lmbHttpRequest('/test_admin_node_with_object/create');

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEqual($response->getResponseString(), 'initCreateForm|');
  }

  function testEventsOnPerformCreateActionWithPost()
  {
    $request = new lmbHttpRequest('/test_admin_node_with_object/create', array(), array('title' => 'test',
                                                                                        'identifier' => 'test',
                                                                                        'field' => 'test'));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|onBeforeCreate|onBeforeSave|onAfterSave|onAfterCreate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformCreateActionWithPostNotValid()
  {
    $request = new lmbHttpRequest('/test_admin_node_with_object/create', array(), array('title' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionFirstTime()
  {
    $node = new lmbCmsNode();
    $node->setIdentifier('test');
    $node->setTitle('test');
    $node->save();

    $request = new lmbHttpRequest('/test_admin_node_with_object/edit/' . $node->getId());

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $this->assertEqual($response->getResponseString(), 'initEditForm|');
  }

  function testEventsOnPerformEditActionWithPostNotValid()
  {
    $node = $this->_createNodeWithObject();

    $request = new lmbHttpRequest('/test_admin_node_with_object/edit/' . $node->getId(), array(), array('id' => $node->getId(), 'title' => ''));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformEditActionWithPost()
  {
    $node = $this->_createNodeWithObject();

    $request = new lmbHttpRequest('/test_admin_node_with_object/edit/' . $node->getId(), array(), array('id' => $node->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeValidate|onAfterValidate|onBeforeEdit|onBeforeSave|onAfterSave|onAfterEdit|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  function testEventsOnPerformDeleteAction()
  {
    $node = $this->_createNodeWithObject();

    $request = new lmbHttpRequest('/test_admin_node_with_object/delete/' . $node->getId(), array(), array('id' => $node->getId()));

    $app = new lmbWebApplicationSandbox();
    $response = $app->imitate($request);

    $expected_callchain = 'onBeforeDelete|onAfterDelete|';
    $this->assertEqual($response->getResponseString(), $expected_callchain);
  }

  protected function _createNodeWithObject()
  {
    $node = new lmbCmsNode();
    $node->setIdentifier('test');
    $node->setTitle('test');

    $object = new NodeObjectForTesting();
    $object->setField('test');
    $node->setObject($object);
    $object->setNode($node);

    $object->save();
    $node->save();
    return $node;
  }
}

?>