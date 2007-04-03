<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbStaticCommandControllerTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/controller/lmbStaticCommandController.class.php');
lmb_require('limb/classkit/src/lmbObject.class.php');

class TestingStaticCommandController extends lmbStaticCommandController
{
  protected $default_action = 'admin_display';
  protected $name = 'testing';

  // To be able to set actions properties right from the tests
  function setTestingActions($actions)
  {
    $this->actions = $actions;
  }
}

class TestingStaticCommandControllerStubCommand implements lmbCommand
{
  public $was_performed = false;

  function perform()
  {
    $this->was_performed = true;
  }
}

class lmbStaticCommandControllerTest extends UnitTestCase
{
  function testGetDifferentProperties()
  {
    $controller = new TestingStaticCommandController();
    $controller->setTestingActions(array('action1' => array(),
                                         'action2' => array('prop2' => 'any_other_value',
                                                            'p' => 1)));

    $this->assertEqual($controller->getName(), 'testing');

    $this->assertEqual(array('action1', 'action2'),
                       $controller->getActionsList());

    $this->assertTrue($controller->actionExists('action1'));
    $this->assertFalse($controller->actionExists('no_such_action'));

    $this->assertEqual($controller->getActionProperties('action2'),
                      array('prop2' => 'any_other_value', 'p' => 1));

    $this->assertEqual($controller->getActionProperties('no-such-action'),
                       array());

    $this->assertEqual($controller->getDefaultAction(), 'admin_display');
  }

  function testPerformActionThrowExceptionIfCommandNotDefined()
  {
    $controller = new TestingStaticCommandController();
    $controller->setCurrentAction($action = 'not_such_action');

    try
    {
      $command = $controller->performAction();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testPerformCommandOk()
  {
    $actions = array('display' => array('title' => 'Display Action',
                                        'command' => $command = new TestingStaticCommandControllerStubCommand()));

    $controller = new TestingStaticCommandController();
    $controller->setTestingActions($actions);
    $controller->setCurrentAction($action = 'display');
    $controller->performAction();
    $this->assertTrue($command->was_performed);
  }

  function testGetActionCommand()
  {
    $actions = array('display' => array('title' => 'Display Action',
                                        'command' => 'TestingStaticCommandControllerStubCommand'));

    $controller = new TestingStaticCommandController();
    $controller->setTestingActions($actions);
    $controller->setCurrentAction($action = 'display');
    $command = $controller->getActionCommand();
    $this->assertTrue($command instanceof TestingStaticCommandControllerStubCommand);
  }

  function testGetActionCommandAsHandleFromArray()
  {
    $actions = array('display' => array('title' => 'Display Action',
                                        'command' => array('limb/web_app/src/command/lmbActionCommand',
                                                           $path = 'path.html')));

    $controller = new TestingStaticCommandController();
    $controller->setTestingActions($actions);
    $controller->setCurrentAction($action = 'display');
    $command = $controller->getActionCommand();
    $this->assertEqual($command->getTemplatePath(), $path);
  }

  function testGetActionCommandAsProxyObject()
  {
    $actions = array('display' => array('title' => 'Display Action',
                                        'command' => new lmbHandle('limb/web_app/src/command/lmbActionCommand',
                                                                   array($path = 'path.html'))));

    $controller = new TestingStaticCommandController();
    $controller->setTestingActions($actions);
    $controller->setCurrentAction($action = 'display');
    $command = $controller->getActionCommand();
    $this->assertEqual($command->getTemplatePath(), $path);
  }

  function testGetActionThrowsExceptionOfInvalidCommandProperty()
  {
    $actions = array('display' => array('title' => 'Display Action',
                                        'command' => new lmbObject()));

    $controller = new TestingStaticCommandController();
    $controller->setTestingActions($actions);
    $controller->setCurrentAction($action = 'display');
    try
    {
      $controller->getActionCommand();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }
}

?>