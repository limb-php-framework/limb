<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreOutputTagTest.class.php 5188 2007-03-06 07:42:21Z serega $
 * @package    wact
 */

class WactCoreOutputTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();
    $template = 'a-<core:output value="Var"/>-z';
    $this->registerTestingTemplate('/tags/core/output/output.html', $template);
  }

  function testSimple()
  {
    $page = $this->initTemplate('/tags/core/output/output.html');
    $page->set('Var', 'test');
    $output = $page->capture();
    $this->assertEqual($output, 'a-test-z');
  }

  function testSetNestedDataSpace()
  {
    $template =
        '<core:SET Var="outer"/>'.
        '<core:output value="Var"/><core:output value="Unassigned"/>:<core:output value="#Var"/><core:output value="#Unassigned"/>' .
        '<core:datasource id="middle">' .
        '<core:SET Var="middle"/>'.
        '-<core:output value="Var"/><core:output value="Unassigned"/>:<core:output value="^Var"/><core:output value="^Unassigned"/>:<core:output value="#Var"/><core:output value="#Unassigned"/>' .
        '<core:datasource id="inner">' .
        '<core:SET Var="inner"/>'.
        '-<core:output value="Var"/><core:output value="Unassigned"/>:<core:output value="^Var"/><core:output value="^Unassigned"/>:<core:output value="#Var"/><core:output value="#Unassigned"/>' .
        '</core:datasource>' .
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/output/set_nested_dataspace.html', $template);
    $page = $this->initTemplate('/tags/core/output/set_nested_dataspace.html');
    $output = $page->capture();
    $this->assertEqual($output, 'outer:outer-middle:outer:outer-inner:middle:outer');
  }

  function testFilter()
  {
    $template = '<core:output value="Var|uppercase"/>';

    $this->registerTestingTemplate('/tags/core/output/filter.html', $template);
    $page = $this->initTemplate('/tags/core/output/filter.html');
    $page->set('Var', 'Foo');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }

  function testFilterChain()
  {
    $template = '<core:output value="Var|trim|uppercase"/>';

    $this->registerTestingTemplate('/tags/core/output/filterchain.html', $template);
    $page = $this->initTemplate('/tags/core/output/filterchain.html');
    $page->set('Var', '   Foo   ');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }
}
?>