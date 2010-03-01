<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/config/src/lmbConfTools.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

class lmbConfToolsTest extends UnitTestCase
{
  /**
   * @var lmbConfTools
   */
  protected $toolkit;

  protected $application_configs_dir;
  protected $package_configs_dir;

  function setUp()
  {
    lmbToolkit :: save();
    $this->toolkit = lmbToolkit :: merge(new lmbConfTools());

    $this->application_configs_dir = lmb_var_dir() . '/app/settings';
    lmbFs::mkdir($this->application_configs_dir);

    $this->package_configs_dir = lmb_var_dir() . '/package/settings';
    lmbFs::mkdir($this->package_configs_dir);

    $tests_include_apth = $this->application_configs_dir . ';' . $this->package_configs_dir;
    $this->toolkit->setConfIncludePath($tests_include_apth);
  }

  function tearDown()
  {
    lmbToolkit :: restore();
    lmbFs::rm($this->application_configs_dir);
    lmbFs::rm($this->package_configs_dir);
  }

  function testSetGetConf()
  {
    $conf_name = 'foo';
    $key = 'bar';
    $value = 42;

    $this->toolkit->setConf($conf_name, array($key => $value));

    $conf = $this->toolkit->getConf($conf_name);
    $this->assertEqual($conf[$key], $value);
  }

  function testHasConf()
  {
    $content = '<?PHP $conf = array("foo" => 42); ';
    lmbFs::safeWrite($this->application_configs_dir . '/has.conf.php', $content);
    $this->assertFalse($this->toolkit->hasConf('not_existed'));
    $this->assertTrue($this->toolkit->hasConf('has'));
  }

  function testGetConf_WithApplicationConfig()
  {
    $content = '<?PHP $conf = array("foo" => 42); ';
    lmbFs::safeWrite($this->application_configs_dir . '/with_app.conf.php', $content);

    $content = '<?PHP $conf = array("bar" => 101); ';
    lmbFs::safeWrite($this->package_configs_dir . '/with_app.conf.php', $content);

    $conf = $this->toolkit->getConf('with_app');

    $this->assertFalse($conf->has('bar'));
    if($this->assertTrue($conf->has('foo')))
      $this->assertEqual($conf->get('foo'), 42);
  }

  function testGetConf_WithoutApplicationConfig()
  {
    $content = '<?PHP $conf = array("bar" => 101); ';
    lmbFs::safeWrite($this->package_configs_dir . '/without_app.conf.php', $content);

    $conf = $this->toolkit->getConf('without_app');

    if($this->assertTrue($conf->has('bar')))
      $this->assertEqual($conf->get('bar'), 101);
  }

  function testGetYamlConf_WithoutApplicationConfig()
  {
    $content = "bar:\n  foo: 200\n  test: string with spaces\n\n";
    lmbFs::safeWrite($this->package_configs_dir . '/without_app.yml', $content);
    $conf = $this->toolkit->getConf('without_app.yml');

    if($this->assertTrue($conf->has('bar')))
    {
      $bar = $conf->get('bar');
      $this->assertEqual($bar['foo'], 200);
      $this->assertEqual($bar['test'], 'string with spaces');
    }
  }

  function testGetYamlConf_WithApplicationConfig()
  {
    $content = "bar:\n  foo: 200\n  test: string with spaces\n\n";
    lmbFs::safeWrite($this->package_configs_dir . '/with_app.yml', $content);

    $content = "bar:\n  foo: 201\n  test: \"string_without_spaces\"\n\n";
    lmbFs::safeWrite($this->application_configs_dir . '/with_app.yml', $content);

    
    $conf = $this->toolkit->getConf('with_app.yml');

    if($this->assertTrue($conf->has('bar')))
    {
      $bar = $conf->get('bar');
      $this->assertEqual($bar['foo'], 201);
      $this->assertEqual($bar['test'], 'string_without_spaces');
    }
  }

  function testGetYamlConf_WithNestedAndInlineProperies()
  {
    $content = "bar:\n  foo: 200\n  test: string with spaces\n\n  nested:  \n    inline: {prop: value}\n";
    lmbFs::safeWrite($this->package_configs_dir . '/without_app.yml', $content);
    $conf = $this->toolkit->getConf('without_app.yml');

    if($this->assertTrue($conf->has('bar')))
    {
      $bar = $conf->get('bar');
      $this->assertEqual($bar['foo'], 200);
      $this->assertEqual($bar['test'], 'string with spaces');
      $this->assertEqual($bar['nested']['inline']['prop'], 'value');
    }
  }
  function testGetYamlConf_WithPhpCodeInside()
  {
    $content = <<<YAML
test:
<?php for(\$i=1; \$i<4; \$i++){ ?>
  bar<?php echo \$i; ?>: <?php echo \$i*2; ?> #enshure that spase exists after closed php tag
<?php } ?>

YAML;
    lmbFs::safeWrite($this->package_configs_dir . '/php.yml', $content);
    $conf = $this->toolkit->getConf('php.yml');

    if($this->assertTrue($conf->has('test')))
    {
      $test = $conf->get('test');
      $this->assertEqual(count($test),3);
      $this->assertEqual($test['bar1'],2);
      $this->assertEqual($test['bar2'],4);
      $this->assertEqual($test['bar3'],6);
      $this->assertFalse(isset($test['bar4']));
    }
  }
}
