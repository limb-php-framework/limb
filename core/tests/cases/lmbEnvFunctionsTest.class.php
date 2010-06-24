<?php

class lmbEnvFunctionsTest extends UnitTestCase
{
  private $_prev_env = array();
  private $_keys = array();

  function setUp()
  {
    $this->_prev_env = $_ENV;
    $_ENV = array();
    $this->_keys = array();
  }

  function tearDown()
  {
    $_ENV = $this->_prev_env;
  }

  function testGetNullByDefault()
  {
    $this->assertNull(lmb_env_get($this->_('foo')));
  }

  function testGetDefault()
  {
    $this->assertEqual(lmb_env_get($this->_('foo'), 1), 1);
  }

  function testGetWithDefinedConstant()
  {
      define($this->_('foo'), 'bar');
      $this->assertEqual(lmb_env_get($this->_('foo')), 'bar');
  }

  function testSet()
  {
    lmb_env_set($this->_('foo'), 'bar');
    $this->assertEqual(lmb_env_get($this->_('foo')), 'bar');
  }

  function testSetOr()
  {
    lmb_env_setor($this->_('foo'), 'bar');
    $this->assertEqual(lmb_env_get($this->_('foo')), 'bar');

    lmb_env_setor($this->_('foo'), 'baz');
    $this->assertEqual(lmb_env_get($this->_('foo')), 'bar');
  }

  function testSetOrWithDefinedConstant()
  {
      define($this->_('foo'), 'bar');

      lmb_env_setor($this->_('foo'), 'baz');
      $this->assertEqual(lmb_env_get($this->_('foo')), 'bar');
  }

  function testHas()
  {
    $this->assertFalse(lmb_env_has($this->_('foo')));
    lmb_env_set($this->_('foo'), 'bar');
    $this->assertTrue(lmb_env_has($this->_('foo')));
  }

  function testHasWorksForNulls()
  {
    $this->assertFalse(lmb_env_has($this->_('foo')));
    lmb_env_set($this->_('foo'), null);
    $this->assertTrue(lmb_env_has($this->_('foo')));
  }

  function testSetDefinesConstant()
  {
    $this->assertFalse(defined($this->_('foo')));
    lmb_env_set($this->_('foo'), 'bar');
    $this->assertEqual(constant($this->_('foo')), 'bar');
  }

  function testHasAndGetFallbackToConstant()
  {
    $name = $this->_('LIMB_TEST_FOO');

    $this->assertFalse(lmb_env_has($name));
    $this->assertNull(lmb_env_get($name, null));

    define($name, 'bar');
    $this->assertTrue(lmb_env_has($name));
    $this->assertEqual(lmb_env_get($name), 'bar');
  }

  function testRemove()
  {
    lmb_env_set('foo_remove', 'bar');
    $this->assertTrue(lmb_env_has('foo_remove'));
    $this->assertEqual(lmb_env_get('foo_remove'), 'bar');

    lmb_env_remove('foo_remove');
    $this->assertFalse(lmb_env_has('foo_remove'));
    $this->assertEqual(lmb_env_get('foo_remove', $random = mt_rand()), $random);
  }

  function testTrace()
  {
    lmb_env_trace($this->_('foo'));

    ob_start();
    lmb_env_setor($key = $this->_('foo'), $value = 'bar');
    $call_line = strval(__LINE__ - 1);
    $trace_info = ob_get_clean();

    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line));
    $this->assertTrue(strstr($trace_info, $method_name = 'setor'));
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));

    ob_start();
    lmb_env_set($key, $value = 'baz');
    $call_line = strval(__LINE__ - 1);
    $trace_info = ob_get_clean();

    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line));
    $this->assertTrue(strstr($trace_info, $method_name = 'set'));
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));
  }

  //used for convenient tracking of the random keys
  private function _($name)
  {
    if(!isset($this->_keys[$name]))
      $this->_keys[$name] = $name . mt_rand() . time();
    return $this->_keys[$name];
  }

  function testLmbVarDir_Get() {
    $old_value = lmb_env_get('LIMB_VAR_DIR');
    $new_value = $old_value.'/';

    lmb_env_set('LIMB_VAR_DIR', $new_value);
    $this->assertIdentical($new_value, lmb_var_dir());

    lmb_env_set('LIMB_VAR_DIR', $old_value);
  }

  function testLmbVarDir_Set() {
    $old_value = lmb_env_get('LIMB_VAR_DIR');
    $new_value = $old_value.'/';

    lmb_var_dir($new_value);
    $this->assertIdentical($new_value, lmb_var_dir());

    lmb_env_set('LIMB_VAR_DIR', $old_value);
  }
}
