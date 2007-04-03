<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInputFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbInputFilter.class.php');

class lmbTestInputFilterStabRule
{
  function apply($data){}
}

Mock :: generate('lmbTestInputFilterStabRule', 'MockInputFilterRule');

class lmbInputFilterTest extends UnitTestCase
{
  function testAddFilter()
  {
    $input = array('foo' => 'Foo', 'bar' => 'Bar', 'zoo' => 'Zoo');
    $input_filter =& new lmbInputFilter();

    $r1 =& new MockInputFilterRule();
    $r1->expectOnce('apply', array($input));
    $r1->setReturnValue('apply', $sub_res = array('foo' => 'Foo', 'bar' => 'Bar'), array($input));

    $r2 =& new MockInputFilterRule();
    $r2->expectOnce('apply', array($sub_res));
    $r2->setReturnValue('apply', $expected = array('foo' => 'Foo'), array($sub_res));

    $input_filter->addRule($r1);
    $input_filter->addRule($r2);

    $out = $input_filter->filter($input);
    $this->assertEqual($out, $expected);
  }
}

?>