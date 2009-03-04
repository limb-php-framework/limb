<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactCoreDefaultTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $template = '<core:default for="{$Var}">default</core:default>'.
                '<core:optional for="{$Var}">optional</core:optional>';
    $this->registerTestingTemplate('/tags/core/default/default.html', $template);
  }

  function testDefaultUnset()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultString()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', 'test');
    $output = $page->capture();
    $this->assertEqual($output, 'optional');
  }

  function testDefaultZeroString()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', '0');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultEmptyString()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', '');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultEmptyLargeString()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', '  ');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultJunkyString()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', "\n\t\n ");
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultEmptyArray()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', array());
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultNonEmptyArray()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', array(1));
    $output = $page->capture();
    $this->assertEqual($output, 'optional');
  }

  function testDefaultNumber()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', 99);
    $output = $page->capture();
    $this->assertEqual($output, 'optional');
  }

  function testDefaultZero()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', 0);
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultNull()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', NULL);
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultTrue()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', TRUE);
    $output = $page->capture();
    $this->assertEqual($output, 'optional');
  }

  function testDefaultFalse()
  {
    $page = $this->initTemplate('/tags/core/default/default.html');
    $page->set('Var', FALSE);
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultMissingFor()
  {
    $template = '<core:default>default</core:default>';
    $this->registerTestingTemplate('/tags/core/default/missingfor.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/default/missingfor.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
    }
  }

  function testDBKept()
  {
    $template = '<core:default for="Var">default</core:default>'.
                '<core:optional for="Var">optional</core:optional>';
    $this->registerTestingTemplate('/tags/core/default/default_bc_kept.html', $template);
    $page = $this->initTemplate('/tags/core/default/default_bc_kept.html');
    $page->set('Var', 1);
    $output = $page->capture();
    $this->assertEqual($output, 'optional');
  }
}

