<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/macro/src/lmbMacroTemplateLocator.class.php');
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

Mock :: generate('lmbMacroTemplateLocator', 'MockMacroTemplateLocator');
Mock :: generate('lmbValidationRule', 'MockValidationRule');

class TestingController extends lmbController
{
  protected $name = 'foo';
  public $display_performed = false;
  public $template_name;

  function doDisplay()
  {
    $this->display_performed = true;
    $this->template_name = $this->view->getTemplate();
  }

  function doWrite()
  {
    return "Hi!";
  }

  function doSetVars()
  {
    $this->item = 'item';
  }

  function doPopup()
  {
    $this->closePopup();
  }

  function doWithoutPopup()
  {
    $this->in_popup = false;
    $this->closePopup();
  }

  function addValidatorRule($r)
  {
    $this->validator->addRule($r);
  }

  function getErrorList()
  {
    return $this->error_list;
  }

  function set($name, $value)
  {
    $this->$name = $value;
  }
}

class SecondTestingController extends lmbController {}

class TestingForwardController extends lmbController
{
  function __construct()
  {
    parent::__construct();

    $this->forward('testing', 'write');
  }
}

class lmbControllerTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testActionExists()
  {
    $controller = new TestingController();
    $this->assertTrue($controller->actionExists('display'));
    $this->assertFalse($controller->actionExists('no_such_action'));
  }

  function testGuessControllerName()
  {
    $controller = new SecondTestingController();
    $this->assertEqual($controller->getName(), 'second_testing');
  }

  function testPerformAction()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('display');
    $controller->performAction();
    $this->assertTrue($controller->display_performed);
  }

  function testPerformedActionStringResultIsWrittenToResponse()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('write');
    $controller->performAction();
    $this->assertEqual($this->toolkit->getResponse()->getResponseString(), "Hi!");
  }

  function testSetTemplateOnlyIfMethodIsNotFound()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => 'lmbDummyView'));

    $controller = new TestingController();
    $controller->setCurrentAction('detail');

    $controller->performAction();
    $this->assertTrue($this->toolkit->getView()->getTemplate(), 'testing/detail.html');
  }

  function testGuessingTemplateWorksOkForActionWithPercentageSymbol()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => 'lmbDummyView'));

    $controller = new TestingController();
    $controller->setCurrentAction('detail%28');

    $controller->performAction();
    $this->assertTrue($this->toolkit->getView()->getTemplate(), 'testing/detail%28.html');
  }

  function testControllerAttributesAutomaticallyPassedToView()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => 'lmbDummyView'));

    $controller = new TestingController();
    $controller->set('foo', 'FOO');
    $controller->set('bar', 'BAR');
    $controller->set('_nope', 'NO');
    $controller->setCurrentAction('set_vars');

    $controller->performAction();
    $view = $this->toolkit->getView();
    $this->assertEqual($view->get('item'), 'item');//this one is set in action
    $this->assertEqual($view->get('foo'), 'FOO');
    $this->assertEqual($view->get('bar'), 'BAR');
    $this->assertNull($view->get('_nope'));//this one is ignored, since it's "protected" with _
  }

  function testActionExistsReturnsTrueIsTemplateFound()
  {
    $this->toolkit->setSupportedViewTypes(array('.html' => 'lmbDummyView'));

    $controller = new TestingController();
    $this->assertTrue($controller->actionExists('detail'));
  }

  function testValidateOk()
  {
    $controller = new TestingController();
    $error_list = $controller->getErrorList();

    $ds = new lmbSet();

    $r1 = new MockValidationRule();
    $r1->expectOnce('validate', array($ds, $error_list));

    $r2 = new MockValidationRule();
    $r2->expectOnce('validate', array($ds, $error_list));

    $controller->addValidatorRule($r1);
    $controller->addValidatorRule($r2);

    $this->assertTrue($controller->validate($ds));
  }

  function testValidateFailed()
  {
    $controller = new TestingController();
    $error_list = $controller->getErrorList();
    $error_list->addError('blah!');

    $this->assertFalse($controller->validate(new lmbSet()));
  }

  function testForward()
  {
    $controller = new lmbController();
    $this->assertEqual($controller->forward('testing', 'write'), "Hi!");
  }

  function testForwardInConstructor()
  {
    $testController = new TestingForwardController();
    $this->assertEqual($this->toolkit->getResponse()->getResponseString(), 'Hi!');
    $this->assertFalse($testController->performAction());
  }

  function testClosePopup()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('popup');
    $controller->performAction();
    $this->assertPattern('~^<html><script>~', $this->toolkit->getResponse()->getResponseString());
  }

  function testDoNotClosePopup()
  {
    $controller = new TestingController();
    $controller->setCurrentAction('without_popup');
    $controller->performAction();
    $this->assertEqual('', $this->toolkit->getResponse()->getResponseString());
  }

}


