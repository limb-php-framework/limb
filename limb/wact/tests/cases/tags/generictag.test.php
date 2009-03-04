<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactGenericHTMLTagTestCase extends WactTemplateTestCase
{
  function testWactGenericHTMLTag()
  {
    $template = '<Img ID="runtime" runat="server" />';

    $this->registerTestingTemplate('/tags/generictag1.html', $template);
    $page = $this->initTemplate('/tags/generictag1.html');

    $Component = $page->getChild('runtime');

    $Component->setAttribute("Src", "Img.gif");

    $output = $page->capture();
    $this->assertEqual($output, '<Img ID="runtime" Src="Img.gif" />');
  }

  function testWactGenericContainerHTMLTag()
  {
    $template = '<DIV ID="runtime" runat="server">Testing<br />Testing</DIV>';

    $this->registerTestingTemplate('/tags/generictag2.html', $template);
    $page = $this->initTemplate('/tags/generictag2.html');

    $Component = $page->getChild('runtime');

    $Component->setAttribute("Align", "center");

    $output = $page->capture();
    $this->assertEqual($output, '<DIV ID="runtime" Align="center">Testing<br />Testing</DIV>');

  }

  function testWactGenericContainerHTMLTagNesting()
  {
    $template = '<DIV ID="runtime" runat="server"><DIV></DIV></DIV>';

    $this->registerTestingTemplate('/tags/generictag3.html', $template);
    $page = $this->initTemplate('/tags/generictag3.html');

    $Component = $page->getChild('runtime');

    $Component->setAttribute("Align", "center");

    $output = $page->capture();
    $this->assertEqual($output, '<DIV ID="runtime" Align="center"><DIV></DIV></DIV>');
  }

  function testWactServerComponentTagIsNotClosed()
  {
    $template = '<DIV ID="runtime" runat="server"><DIV></DIV>';

    $this->registerTestingTemplate('/tags/generictag_not_closed.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/generictag_not_closed.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), 'DIV');
    }
  }

  function testWactGenericContainerHTMLTagNestingCaseInsensitive()
  {
    $template = '<DIV ID="runtime" runat="server"><DIV></DIV></DIV>';

    $this->registerTestingTemplate('/tags/generictag4.html', $template);
    $page = $this->initTemplate('/tags/generictag4.html');

    $Component = $page->getChild('runtime');

    $Component->setAttribute("Align", "center");

    $output = $page->capture();
    $this->assertEqual($output, '<DIV ID="runtime" Align="center"><DIV></DIV></DIV>');

  }

  function testNotAServerComponent()
  {
    $template = '<DIV><DIV ID="runtime" runat="client"></DIV></DIV>';

    $this->registerTestingTemplate('/tags/generictag5.html', $template);
    $page = $this->initTemplate('/tags/generictag5.html');

    $this->assertFalse($page->findChild('runtime'));

    $output = $page->capture();
    $this->assertEqual($output, '<DIV><DIV ID="runtime"></DIV></DIV>');

  }

  function testNotAServerComponent2()
  {
    $template = '<DIV><DIV runat="client"></DIV></DIV>';

    $this->registerTestingTemplate('/tags/generictag6.html', $template);
    $page = $this->initTemplate('/tags/generictag6.html');

    $this->assertFalse($page->findChild('runtime'));

    $output = $page->capture();
    $this->assertEqual($output, '<DIV><DIV></DIV></DIV>');
  }

  function testBrTag()
  {
    $template = '<br id="runtime" runat="server"/>';

    $this->registerTestingTemplate('/tags/generictag7.html', $template);
    $page = $this->initTemplate('/tags/generictag7.html');

    $Component =  $page->findChild('runtime');
  }

  function testPTag() {
    $template = '<p id="runtime" runat="server"></p>';

    $this->registerTestingTemplate('/tags/generictag11.html', $template);
    $page = $this->initTemplate('/tags/generictag11.html');

    $Component =  $page->findChild('runtime');
  }

  function testGenericContainerCaseMismatch()
  {
    $template = '<DiV id="runtime" runat="server">Test</dIv>';

    $this->registerTestingTemplate('/tags/generictag13.html', $template);
    $page = $this->initTemplate('/tags/generictag13.html');

    $Component =  $page->findChild('runtime');

    $output = $page->capture();

    $this->assertEqual($output, '<DiV id="runtime">Test</DiV>');
  }
}

