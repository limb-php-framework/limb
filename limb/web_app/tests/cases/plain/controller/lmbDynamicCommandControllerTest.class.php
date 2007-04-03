<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDynamicCommandControllerTest.class.php 5372 2007-03-28 09:06:30Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/controller/lmbDynamicCommandController.class.php');
lmb_require('limb/view/src/wact/lmbWactTemplateLocator.class.php');

Mock :: generate('lmbWactTemplateLocator', 'MockWactTemplateLocator');

class TestingDynamicCommandController extends lmbDynamicCommandController
{
  protected $name = 'foo';
}

class lmbDynamicCommandControllerTest extends UnitTestCase
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

  function testReturnCommandHandleIfCommandClassFileFound()
  {
    $pkg_dir = $this->_createPackageWithCommand('foo-pkg', 'foo', 'FooBarCommand');

    set_include_path($pkg_dir . PATH_SEPARATOR . get_include_path());

    $controller = new TestingDynamicCommandController();
    $controller->setCurrentAction('bar');
    $command = $controller->getActionCommand();
    $this->assertEqual($command->getClass(), 'FooBarCommand');

    lmbFs :: rm($pkg_dir);
  }

  function testReturnActionWithTemplateIfCommanClassFileNotFound()
  {
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectOnce('locateSourceTemplate', array('foo/bar.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/bar.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingDynamicCommandController();
    $controller->setCurrentAction('bar');
    $command = $controller->getActionCommand();
    $this->assertEqual($command->getClass(), 'lmbActionCommand');
    $this->assertEqual($command->getTemplatePath(), 'foo/bar.html');
  }

  function testPerformActionInCaseTemplateOnlyWasFound()
  {
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectOnce('locateSourceTemplate', array('foo/bar.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/bar.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingDynamicCommandController();
    $controller->setCurrentAction('bar');
    $command = $controller->performAction();
    $this->assertEqual($this->toolkit->getView()->getTemplate(), 'foo/bar.html');
  }

  function testThrowsExceptionIfBothCommandAndTemplateNotFound()
  {
    $controller = new TestingDynamicCommandController();
    $controller->setCurrentAction('bar');
    try
    {
      $command = $controller->getActionCommand();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testActionExistsReturnTrueIfCommandFound()
  {
    $pkg_dir = $this->_createPackageWithCommand('foo-pkg', 'foo', 'FooBarCommand');

    set_include_path($pkg_dir . PATH_SEPARATOR . get_include_path());

    $controller = new TestingDynamicCommandController();
    $this->assertTrue($controller->actionExists('bar'));
    $this->assertFalse($controller->actionExists('no_such_action'));

    lmbFs :: rm($pkg_dir);
  }

  function testActionExistsReturnTrueIfTemplateFound()
  {
    $mock_locator = new MockWactTemplateLocator();
    $mock_locator->expectCallCount('locateSourceTemplate', 2);
    $mock_locator->setReturnValue('locateSourceTemplate', true, array('foo/bar.html'));
    $mock_locator->setReturnValue('locateSourceTemplate', false, array('foo/no_such_action.html'));
    $this->toolkit->setWactLocator($mock_locator);

    $controller = new TestingDynamicCommandController();
    $this->assertTrue($controller->actionExists('bar'));
    $this->assertFalse($controller->actionExists('no_such_action'));

  }

  protected function _createPackageWithCommand($package, $controller, $command)
  {
    $dir = LIMB_VAR_DIR . '/pkg-tmp/' . $package;
    lmbFs :: mkdir("$dir/src/command/$controller");

    file_put_contents("$dir/src/command/$controller/$command.class.php", "<?php class $command{} ?>");

    return $dir;
  }
}

?>