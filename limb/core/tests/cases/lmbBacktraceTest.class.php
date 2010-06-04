<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbBacktrace.class.php');

class lmbBacktraceTest extends UnitTestCase
{
  function testConstruct()
  {
    $backtrace = new lmbBacktrace();
    $given = $backtrace->get();
    $this->assertEqual('array', gettype($given));
    $this->assertEqual('lmbBacktraceTest', $given[0]['class']);
    $this->assertEqual('testConstruct', $given[0]['function']);
  }

  function testConstruct_CustomBacktrace()
  {
    $backtrace = new lmbBacktrace($trace = debug_backtrace());
    $given = $backtrace->get();
    $this->assertEqual('array', gettype($given));
    $this->assertIdentical($trace, $given);
  }

  function testConstruct_Limit()
  {
    $backtrace = new lmbBacktrace($trace = debug_backtrace(), $limit = 2);
    $given = $backtrace->get();
    $this->assertEqual('array', gettype($given));
    $this->assertEqual($limit, count($given));
    $this->assertEqual($trace[0], $given[0]);
  }

  function testConstruct_Offset()
  {
    $backtrace = new lmbBacktrace($trace = debug_backtrace(), $limit = 2, $offset = 1);
    $given = $backtrace->get();
    $this->assertEqual('array', gettype($given));
    $this->assertEqual($limit, count($given));
    $this->assertEqual($trace[$offset], $given[0]);
  }

  function testGetContext()
  {
    $backtrace = new lmbBacktrace($trace = debug_backtrace());
    $this->assertEqual($trace[0], $backtrace->getContext());
  }

  function testToString()
  {
    $backtrace = $this->_createBacktrace($foo = 42, $bar = 'baz');
    $this->assertPattern('/lmbBacktraceTest/', $backtrace->toString());
    $this->assertPattern('/_createBacktrace/', $backtrace->toString());
    $this->assertPattern('/42/', $backtrace->toString());
    $this->assertPattern('/baz/', $backtrace->toString());
  }

  function testCreate()
  {
    $backtrace = new lmbBacktrace();
    $backtrace2 = lmbBacktrace::create(null, null, $backtrace->get());
    $this->assertEqual($backtrace, $backtrace2);
  }

  function testToStringWithResourceArg()
  {
    $resource = fopen(__FILE__, 'r');
    $backtrace = $this->_createBacktrace($resource, 1);
    fclose($resource);
    $this->assertPattern('/RESOURCE\[stream\]/', $backtrace->toString());
  }

  protected function _createBacktrace($foo, $bar)
  {
    return new lmbBacktrace();
  }
}