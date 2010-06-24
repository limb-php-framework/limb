<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFunctionBasedFilterTestFunctionFilter extends lmbMacroFunctionBasedFilter 
{
  protected $function = 'lmb_macro_function_based_filter_test_function';
}

class lmbMacroFunctionBasedFilterTestCallbackFilter extends lmbMacroFunctionBasedFilter 
{
  protected $function = array('lmbMacroFunctionBasedFilterTestClass', 'test');
}

class FakeBase
{
  protected $foo = 42;
  
  function getValue()
  {
    return $this->foo;
  }
}

class lmbMacroFunctionBasedFilterTest extends lmbBaseMacroTest
{
  function testFunction()
  {
    $fake_base = new FakeBase();
    $obj = new lmbMacroFunctionBasedFilterTestFunctionFilter($fake_base);
    $obj->setParams(array('"foo"'));
    $this->assertEqual('lmb_macro_function_based_filter_test_function(42,"foo")', $obj->getValue());
  }
  
  function testCallback()
  {
    $fake_base = new FakeBase();
    $obj = new lmbMacroFunctionBasedFilterTestCallbackFilter($fake_base);
    $obj->setParams(array('"foo"'));
    $this->assertEqual('lmbMacroFunctionBasedFilterTestClass::test(42,"foo")', $obj->getValue());
  }
}
