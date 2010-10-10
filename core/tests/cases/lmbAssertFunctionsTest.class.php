<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbAssertFunctionsTest extends UnitTestCase
{
  function testAssertTrue()
  {
    $this->_checkPositive('lmb_assert_true', array(true));
    $this->_checkNegative('lmb_assert_true', array(false));

    $this->_checkPositive('lmb_assert_true', array(1));
    $this->_checkNegative('lmb_assert_true', array(0));

    $this->_checkPositive('lmb_assert_true', array(1.1));
    $this->_checkNegative('lmb_assert_true', array(0.0));

    $this->_checkPositive('lmb_assert_true', array('foo'));
    $this->_checkNegative('lmb_assert_true', array(''));

    $this->_checkPositive('lmb_assert_true', array(array(1)));
    $this->_checkNegative('lmb_assert_true', array(array()));

    $this->_checkPositive('lmb_assert_true', array(new stdClass()));
  }

  function testAssertTrue_Message()
  {
  	$exception = $this->_checkNegative('lmb_assert_true', array(false));
  	$this->assertPattern('/Value must be positive/', $exception->getMessage());

  	$exception = $this->_checkNegative('lmb_assert_true', array(false, 'foo'));
    $this->assertPattern('/foo/', $exception->getMessage());
  }

  function testAssertTrue_ExceptionClass()
  {
    $exception = $this->_checkNegative('lmb_assert_true', array(false));
    $this->assertIsA($exception, 'lmbInvalidArgumentException');

    $exception = $this->_checkNegative('lmb_assert_true', array(false, null, 'lmbAssertExceptionForTest'));
    $this->assertIsA($exception, 'lmbAssertExceptionForTest');
  }

  function testAssertType_Bool()
  {
    $types = array(
      array(
        'names' => array('bool', 'boolean'),
        'values'=> array(true, false)
      ),
      array(
        'names' => array('integer', 'numeric', 'int'),
        'values'=> array(42, 0, -1)
      ),
      array(
        'names' => array('float', 'double', 'real'),
        'values'=> array(42.1, 0.0, 0xffffffffffffffff)
      ),
      array(
        'names' => array('string'),
        'values'=> array('test'),
      ),
      array(
        'names' => array('array'),
        'values'=> array(array())
      ),
      array(
        'names' => array('object'),
        'values'=> array(new stdClass())
      ),
    );

    foreach ($types as $type)
      foreach ($type['names'] as $type_name)
        foreach ($type['values'] as $value)
          $this->_checkPositive('lmb_assert_type', array($value, $type_name));

    foreach ($types as $key => $type)
      foreach ($types as $another_key => $another_type)
      {
        if ($key == $another_key)
          continue;

        foreach ($type['names'] as $type_name)
        {
          foreach ($another_type['values'] as $another_type_value)
          {
            $this->_checkNegative('lmb_assert_type', array($another_type_value, $type_name));
          }
        }
      }

  }

  function testAssertType_ArrayAccessAsArray()
  {
    $this->_checkNegative('lmb_assert_type', array(new stdClass(), 'array'));
    $this->_checkPositive('lmb_assert_type', array(new ArrayObject(), 'array'));
  }

  function testAssertType_Objects()
  {
    $this->_checkPositive('lmb_assert_type', array(new ArrayObject(), 'ArrayAccess'));
    $this->_checkPositive('lmb_assert_type', array(new ArrayObject(), 'ArrayObject'));

    $this->_checkNegative('lmb_assert_type', array(new ArrayObject(), 'Foo'));
  }

  function testAssertType_CustomMessage()
  {
  	$exception = $this->_checkNegative('lmb_assert_type', array(false, 'string'));
    $this->assertPattern('/Value must be a string type, but boolean given/', $exception->getMessage());

    $exception = $this->_checkNegative('lmb_assert_type', array(false, 'string', '%expected%|%given%'));
    $this->assertPattern('/string|boolean/', $exception->getMessage());
  }

  function testAssertType_ExceptionClass()
  {
    $exception = $this->_checkNegative('lmb_assert_type', array(false, 'string'));
    $this->assertIsA($exception, 'lmbInvalidArgumentException');

    $exception = $this->_checkNegative('lmb_assert_type', array(false, 'string', null, 'lmbAssertExceptionForTest'));
    $this->assertIsA($exception, 'lmbAssertExceptionForTest');
  }

  function testAssertArrayWithKey()
  {
    $this->_checkNegative('lmb_assert_array_with_key', array(
      'not_array', 'needle'
    ));
    $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1), 'bar'
    ));

    $this->_checkPositive('lmb_assert_array_with_key', array(
      array('foo' => 1), 'foo'
    ));
    $this->_checkPositive('lmb_assert_array_with_key', array(
      new ArrayObject(array('foo' => 1)), 'foo'
    ));
  }

  function testAssertArrayWithKey_MultipleCheck()
  {
    $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1, 'bar' => 2), array('foo', 'baz')
    ));
    $this->_checkPositive('lmb_assert_array_with_key', array(
      array('foo' => 1, 'bar' => 2), array('foo', 'bar')
    ));
  }

  function testAssertArrayWithKey_Message()
  {
    $exception = $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1), array('bar', 'baz')
    ));
    $this->assertEqual(
      "Value is not an array or doesn't have a key(s) \"bar, baz\"",
      $exception->getOriginalMessage()
    );

    $exception = $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1), array('bar', 'baz'), 'foo|%keys%'
    ));
    $this->assertEqual(
      'foo|bar, baz',
      $exception->getOriginalMessage()
    );
  }

  function testAssertArrayWithKey_ExceptionClass()
  {
    $exception = $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1), array('bar', 'baz')
    ));
    $this->assertIsA($exception, 'lmbInvalidArgumentException');

    $exception = $this->_checkNegative('lmb_assert_array_with_key', array(
      array('foo' => 1), array('bar', 'baz'), null, 'lmbAssertExceptionForTest'
    ));
    $this->assertIsA($exception, 'lmbAssertExceptionForTest');
  }

  function testAssertRegExp()
  {
    $this->_checkNegative('lmb_assert_reg_exp', array(array(), 'foo'));

    $this->_checkPositive('lmb_assert_reg_exp', array('foomatic', 'foo'));
    $this->_checkNegative('lmb_assert_reg_exp', array('bar', 'foo'));

    $this->_checkPositive('lmb_assert_reg_exp', array('abc', '/bc/'));
    $this->_checkNegative('lmb_assert_reg_exp', array('abc', '/xy/'));

    Mock::generate('stdClass', 'MockStringProvider', array('__toString'));
    $string_provider = new MockStringProvider;
    $string_provider->expectAtLeastOnce('__toString');
    $string_provider->setReturnValue('__toString', 'abc');

    $this->_checkPositive('lmb_assert_reg_exp', array($string_provider, '/bc/'));
    $this->_checkNegative('lmb_assert_reg_exp', array($string_provider, '/xy/'));
  }

  function testAssertRegExp_Message()
  {
    $exception = $this->_checkNegative('lmb_assert_reg_exp', array('bar', 'foo'));
    $this->assertEqual('Value is not an string or pattern "foo" not found', $exception->getOriginalMessage());

    $exception = $this->_checkNegative('lmb_assert_reg_exp', array('bar', 'foo', 'baz|%pattern%'));
    $this->assertEqual('baz|foo', $exception->getOriginalMessage());
  }

  function testAssertRegExp_ExceptionClass()
  {
    $exception = $this->_checkNegative('lmb_assert_true', array(false));
    $this->assertIsA($exception, 'lmbInvalidArgumentException');

    $exception = $this->_checkNegative('lmb_assert_true', array(false, null, 'lmbAssertExceptionForTest'));
    $this->assertIsA($exception, 'lmbAssertExceptionForTest');
  }

  protected function _checkPositive($function, $params)
  {
    call_user_func_array($function, $params);
    $this->pass();
  }

  /**
   * @return lmbException
   */
  protected function _checkNegative($function, $params = array())
  {
    try
    {
      call_user_func_array($function, $params);
      $message = "fail {$function}(".(var_export($params, true)).').';
      $this->fail($message);
    }
    catch(lmbInvalidArgumentException $e)
    {
      $this->pass();
      return $e;
    }
  }
}

class lmbAssertExceptionForTest extends lmbInvalidArgumentException {}
