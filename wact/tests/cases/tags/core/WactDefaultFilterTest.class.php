<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactDefaultFilterTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $template = '{$Var|default:"default"}';
    $this->registerTestingTemplate('/tags/core/default-filter/default.html', $template);
  }

  function testDefaultUnset()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultString()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', 'test');
    $output = $page->capture();
    $this->assertEqual($output, 'test');
  }

  function testDefaultZeroString()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', '0');
    $output = $page->capture();
    $this->assertEqual($output, '0');
  }

  function testDefaultEmptyString()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', '');
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultNumber()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', 99);
    $output = $page->capture();
    $this->assertEqual($output, '99');
  }

  function testDefaultZero()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', 0);
    $output = $page->capture();
    $this->assertEqual($output, '0');
  }

  function testDefaultNull()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', NULL);
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }

  function testDefaultTrue()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', TRUE);
    $output = $page->capture();
    $this->assertEqual($output, '1');
  }

  function testDefaultFalse()
  {
    $page = $this->initTemplate('/tags/core/default-filter/default.html');
    $page->set('Var', FALSE);
    $output = $page->capture();
    $this->assertEqual($output, 'default');
  }
}

