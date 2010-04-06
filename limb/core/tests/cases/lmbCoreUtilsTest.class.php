<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbCoreUtilsTest extends UnitTestCase
{
  function testCamelCaseUcfirst()
  {
    $this->assertEqual(lmb_camel_case('foo'), 'Foo');
    $this->assertEqual(lmb_camel_case('foo_bar'), 'FooBar');
    $this->assertEqual(lmb_camel_case('foo168_bar'), 'Foo168Bar');
    $this->assertEqual(lmb_camel_case('foo_bar_hey_wow'), 'FooBarHeyWow');
    $this->assertEqual(lmb_camel_case('_foo_bar'), '_FooBar');
    $this->assertEqual(lmb_camel_case('_foo_bar_'), '_FooBar_');
    $this->assertEqual(lmb_camel_case('___foo___'), '___Foo___');
  }

  function testCamelCaseDontUcfirst()
  {
    $this->assertEqual(lmb_camel_case('foo', false), 'foo');
    $this->assertEqual(lmb_camel_case('foo_bar', false), 'fooBar');
    $this->assertEqual(lmb_camel_case('foo168_bar', false), 'foo168Bar');
    $this->assertEqual(lmb_camel_case('foo_bar_hey_wow', false), 'fooBarHeyWow');
    $this->assertEqual(lmb_camel_case('_foo_bar', false), '_fooBar');
    $this->assertEqual(lmb_camel_case('_foo_bar_', false), '_fooBar_');
    $this->assertEqual(lmb_camel_case('___foo___', false), '___foo___');
  }
  
  function testUnderScores()
  {
    $this->assertEqual(lmb_under_scores('FooBar'), 'foo_bar');
    $this->assertEqual(lmb_under_scores('Foo168Bar'), 'foo168_bar');
    $this->assertEqual(lmb_under_scores('FooBarZoo'), 'foo_bar_zoo');
    $this->assertEqual(lmb_under_scores('_FooBarZoo'), '_foo_bar_zoo');
    $this->assertEqual(lmb_under_scores('_FooBarZoo_'), '_foo_bar_zoo_');
  }
  
  function testPlural()
  {
    //$this->assertEqual(lmb_plural('dog'), 'dogs');
    $this->assertEqual(lmb_plural('glass'), 'glasses');
    $this->assertEqual(lmb_plural('dictionary'), 'dictionaries');    
    $this->assertEqual(lmb_plural('boy'), 'boys');    
    $this->assertEqual(lmb_plural('half'), 'halves');
    $this->assertEqual(lmb_plural('man'), 'men');
  }

  function testCamelCaseWithNumbers()
  {
    $this->assertEqual(lmb_camel_case('foo_0'), 'Foo_0');
    $this->assertEqual(lmb_camel_case('foo_1_bar'), 'Foo_1Bar');
  }

  function testUnderScoresWithNumbers()
  {
    $this->assertEqual(lmb_under_scores('Foo_0'), 'foo_0');
    $this->assertEqual(lmb_under_scores('Foo_1Bar'), 'foo_1_bar');
  }
}

