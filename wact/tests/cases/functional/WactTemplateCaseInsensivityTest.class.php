<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

/**
* Tests that WACT templates are case insensitive, but case preserving.
*/
class WactTemplateCaseInsensivityTest extends WactTemplateTestCase
{
  function testAttributeCase()
  {
    $template = '<tag one="one" Two="Two" THREE="THREE">contents</tag>';

    $this->registerTestingTemplate('/tags/case/attributecase.html', $template);
    $page = $this->initTemplate('/tags/case/attributecase.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testWactAttributeCase()
  {
    $template = '<form id="test" one="one" Two="Two" THREE="THREE">contents</form>';

    $this->registerTestingTemplate('/tags/case/wactattributecase.html', $template);
    $page = $this->initTemplate('/tags/case/wactattributecase.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testTagCase()
  {
    $template = '<BR /><br /><Br />';

    $this->registerTestingTemplate('/tags/case/tagcase.html', $template);
    $page = $this->initTemplate('/tags/case/tagcase.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testWactTagCase()
  {
    $template = '<FORM id="test1"></FORM><Form id="test2"></Form><form id="test3"></form>';

    $this->registerTestingTemplate('/tags/case/wacttagcase.html', $template);
    $page = $this->initTemplate('/tags/case/wacttagcase.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }
}

