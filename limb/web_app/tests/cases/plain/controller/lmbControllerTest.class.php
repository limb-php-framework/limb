<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbControllerTest.class.php 5628 2007-04-11 12:09:20Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/view/src/wact/lmbWactTemplateLocator.class.php');
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

Mock :: generate('lmbWactTemplateLocator', 'MockWactTemplateLocator');
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

  function addValidatorRule($r)
  {
    $this->validator->addRule($r);
  }

  function getErrorList()
  {
    return $this->error_list;
  }
}

class SecondTestingController extends lmbController {}

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

  function testPerformCommand()
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
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectOnce('locateSourceTemplate', array('foo/detail.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/detail.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingController();
    $controller->setCurrentAction('detail');

    $controller->performAction();
    $this->assertTrue($this->toolkit->getView()->getTemplate(), 'testing/detail.html');
  }

  function testActionExistsReturnsTrueIsTemplateFound()
  {
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectOnce('locateSourceTemplate', array('foo/detail.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/detail.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingController();
    $this->assertTrue($controller->actionExists('detail'));
  }

  function setTemplateIfItExistsBeforePerformingAction()
  {
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectOnce('locateSourceTemplate', array('foo/detail.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/detail.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingController();
    $controller->setCurrentAction('detail');

    $controller->performAction();
    $this->assertTrue($controller->template_name, 'testing/detail.html');
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
}

?>