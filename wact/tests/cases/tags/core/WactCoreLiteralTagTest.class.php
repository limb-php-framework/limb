<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactCoreLiteralTagTest extends WactTemplateTestCase
{
  function testLiteralReference()
  {
    $template = '<core:literal>{$Reference}</core:literal>';

    $this->registerTestingTemplate('/tags/core/literal/reference.html', $template);
    $page = $this->initTemplate('/tags/core/literal/reference.html');
    $output = $page->capture();
    $this->assertEqual($output, '{$Reference}');
  }

  function testLiteralTag()
  {
    $template = '<core:literal><core:block></core:literal>';

    $this->registerTestingTemplate('/tags/core/literal/tag.html', $template);
    $page = $this->initTemplate('/tags/core/literal/tag.html');
    $output = $page->capture();
    $this->assertEqual($output, '<core:block>');
  }

  function testLiteralNested1()
  {
    $template = '<core:literal><core:literal></core:literal>';

    $this->registerTestingTemplate('/tags/core/literal/nested1.html', $template);
    $page = $this->initTemplate('/tags/core/literal/nested1.html');
    $output = $page->capture();

    $this->assertEqual($output, '<core:literal>');
  }

  function testServerIdInLiteral()
  {
    $template = '<core:literal><form runat="server"></form></core:literal>';

    $this->registerTestingTemplate('/tags/core/literal/serveridinliteral.html', $template);

    $page = $this->initTemplate('/tags/core/literal/serveridinliteral.html');
    $output = $page->capture();

    $this->assertEqual($output, '<form></form>');
  }

  function testServerIdClientInLiteral()
  {
    $template = '<core:literal><form runat="client"></form></core:literal>';

    $this->registerTestingTemplate('/tags/core/literal/serveridclientinliteral.html', $template);

    $page = $this->initTemplate('/tags/core/literal/serveridclientinliteral.html');
    $output = $page->capture();

    $this->assertEqual($output, '<form></form>');
  }
}

