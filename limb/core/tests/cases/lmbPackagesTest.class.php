<?php

lmb_require('core/src/exception/lmbException.class.php');

class lmbPackagesFunctionsTest extends UnitTestCase
{
  public static $counter = 0;
  protected $_prev_env = array();

  function setUp()
  {
    $this->_prev_env = $_ENV;
    $_ENV = array();

    self::$counter = 0;

    $packages_dir = lmb_var_dir() . '/core_packages_test/';
    if(!file_exists($packages_dir))
      mkdir($packages_dir);
    lmb_env_set('LIMB_PACKAGES_DIR', $packages_dir);
  }

  function tearDown()
  {
    $_ENV = $this->_prev_env;
  }

  protected function createPackageMainFile($name, $packages_dir)
  {
    if(!file_exists($packages_dir . $name))
      mkdir($packages_dir . $name);

    $path = $packages_dir . $name . '/common.inc.php';
    $content = '<?php lmbPackagesFunctionsTest::$counter++; lmb_package_register("'.$name.'"); ?>';
    file_put_contents($path, $content);
  }

  function testPackageInclude()
  {
    $this->createPackageMainFile('include', lmb_env_get('LIMB_PACKAGES_DIR'));
    lmb_package_include('include');
    $this->assertIdentical(1, lmbPackagesFunctionsTest::$counter);
  }

  function testPackageInclude_NotExistedPackage()
  {
    try {
      lmb_package_include($name = 'not_existed', $package_dir = 'darkside');
      $this->fail();
    } catch (lmbNoSuchPackageException $e) {
      $this->assertEqual($package_dir, $e->getParam('dir'));
      $this->assertEqual($name, $e->getParam('name'));
    }
  }

  function testPackageInclude_CustomPath()
  {
    $this->createPackageMainFile('include_custom', lmb_var_dir());
    lmb_package_include('include_custom', lmb_var_dir());
    $this->assertIdentical(1, lmbPackagesFunctionsTest::$counter);
  }

  function testPackageRegisterAndRegistered()
  {
    $this->assertFalse(lmb_package_registered('foo'));
    lmb_package_register('foo');
    $this->assertTrue(lmb_package_registered('foo'));
  }

  function testPackageRegisterAndRegistered_CustomPath()
  {
    lmb_package_register('foo', lmb_var_dir());
    $this->assertFalse(lmb_package_registered('foo'));
    $this->assertTrue(lmb_package_registered('foo', lmb_var_dir()));
  }

  function testPackageInclude_ManyTimes()
  {
    $this->createPackageMainFile('include_many', lmb_env_get('LIMB_PACKAGES_DIR'));

    lmb_package_include('include_many');
    lmb_package_include('include_many');

    $this->assertIdentical(1, lmbPackagesFunctionsTest::$counter);
  }

  function testPackagesList()
  {
    $this->assertEqual(array(), lmb_packages_list());

    lmb_package_register('foo');
    lmb_package_register('bar', 'baz/');

    $this->assertEqual(array('foo', 'baz/bar'), lmb_packages_list());
  }


}
