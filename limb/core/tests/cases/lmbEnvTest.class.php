<?php

lmb_require('limb/core/src/lmbEnv.class.php');

class lmbEnvTest extends UnitTestCase
{
  function testGet_Negative()
  {
    $this->assertEqual(lmbEnv::get('foo'), '');
  }

  function testSetGet()
  {
    lmbEnv::add('foo', 'bar');
    $this->assertEqual(lmbEnv::get('foo'), 'bar');
    lmbEnv::add('foo', 'baz');
    $this->assertEqual(lmbEnv::get('foo'), 'bar');
  }

  function testReplace()
  {
    lmbEnv::add('foo', 'bar');
    lmbEnv::set('foo', 'baz');
    $this->assertEqual(lmbEnv::get('foo'), 'baz');
  }
}