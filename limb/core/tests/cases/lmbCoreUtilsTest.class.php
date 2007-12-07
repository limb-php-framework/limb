<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
    $this->assertEqual(lmb_camel_case('___foo___bar'), '___Foo_Bar');
    $this->assertEqual(lmb_camel_case('___foo___bar_hey'), '___Foo_BarHey');
  }

  function testCamelCaseDontUcfirst()
  {
    $this->assertEqual(lmb_camel_case('foo', false), 'foo');
    $this->assertEqual(lmb_camel_case('foo_bar', false), 'fooBar');
    $this->assertEqual(lmb_camel_case('foo168_bar', false), 'foo168Bar');
    $this->assertEqual(lmb_camel_case('foo_bar_hey_wow', false), 'fooBarHeyWow');
    $this->assertEqual(lmb_camel_case('_foo_bar', false), '_fooBar');
    $this->assertEqual(lmb_camel_case('_foo_bar_', false), '_fooBar_');
    $this->assertEqual(lmb_camel_case('___foo___bar', false), '___foo_Bar');
    $this->assertEqual(lmb_camel_case('___foo___bar_hey', false), '___foo_BarHey');
  }
  
  function testUnderScores()
  {
    $this->assertEqual(lmb_under_scores('FooBar'), 'foo_bar');
    $this->assertEqual(lmb_under_scores('Foo168Bar'), 'foo168_bar');
    $this->assertEqual(lmb_under_scores('FooBarZoo'), 'foo_bar_zoo');
    $this->assertEqual(lmb_under_scores('_FooBarZoo'), '_foo_bar_zoo');
    $this->assertEqual(lmb_under_scores('_FooBarZoo_'), '_foo_bar_zoo_');
  }
}

