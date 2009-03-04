<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* These tests are all malformed HTML.  They test the ability of the parser to
* handle garbage input.
*/
class WactBadHtmlTest extends WactTemplateTestCase
{
  function testWactEndTagRequired()
  {
    $template = '<core:BLOCK Variable="Value">'; // ENDTAG_REQUIRED

    $this->registerTestingTemplate('/badhtml/endtagrequired.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/endtagrequired.html');
    }
    catch(WactException $e){}
  }

  function testWactSelfCloseRequired()
  {
    $template = '<core:BLOCK Variable="Value" />'; // ENDTAG_REQUIRED

    $this->registerTestingTemplate('/badhtml/wactselfcloserequired.html', $template);
    $page = $this->initTemplate('/badhtml/wactselfcloserequired.html');
    $output = $page->capture();

    $this->assertEqual($output, '');
  }

  function testExtraClose()
  {
    // We use input becase input is ENTAG_FORBIDDEN
    $template = '<form runat="server"><input type="text"></input></form>';

    $this->registerTestingTemplate('/tags/form/controls/input/extraclose.html', $template);
    try
    {
      $page = $this->initTemplate('/tags/form/controls/input/extraclose.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactLonelyClose()
  {
    $template = '</core:block>';

    $this->registerTestingTemplate('/badhtml/wactlonelyclose.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wactlonelyclose.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactLonelyCloseForbidden()
  {
    $template = '</input>'; // ENDTAG_FORBIDDEN

    $this->registerTestingTemplate('/badhtml/wactlonelycloseforbid.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wactlonelycloseforbid.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testLonelyClose2()
  {
    $template = '</tag>';

    $this->registerTestingTemplate('/badhtml/lonelyclose2.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/lonelyclose2.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactSelfCloseExtraClose()
  {
    $template = '<core:SET Variable="Value" /></core:SET>';

    $this->registerTestingTemplate('/badhtml/wactselfcloseextraclose.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wactselfcloseextraclose.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testAttributeEntity_lt()
  {
    $template = '<tag attribute="<">contents</tag>';

    $this->registerTestingTemplate('/badhtml/attributeentity_lt.html', $template);
    $page = $this->initTemplate('/badhtml/attributeentity_lt.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testAttributeEntity_gt()
  {
    $template = '<tag attribute=">">contents</tag>';

    $this->registerTestingTemplate('/badhtml/attributeentity_gt.html', $template);
    $page = $this->initTemplate('/badhtml/attributeentity_gt.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testAttributeEntity_quot()
  {
    $template = '<tag attribute="\'test\'">contents</tag>';

    $this->registerTestingTemplate('/badhtml/attributeentity_quot.html', $template);
    $page = $this->initTemplate('/badhtml/attributeentity_quot.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testAttributeEntity_amp()
  {
    $template = '<tag attribute="&">contents</tag>';

    $this->registerTestingTemplate('/badhtml/attributeentity_amp.html', $template);
    $page = $this->initTemplate('/badhtml/attributeentity_amp.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function XtestContentEntity_lt()
  {
    $template = '<body><</body>';

    $this->registerTestingTemplate('/badhtml/contententity_lt.html', $template);
    $page = $this->initTemplate('/badhtml/contententity_lt.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testContentEntity_gt()
  {
    $template = '<body>></body>';

    $this->registerTestingTemplate('/badhtml/contententity_gt.html', $template);
    $page = $this->initTemplate('/badhtml/contententity_gt.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testContentEntity_quot()
  {
    $template = '<body>"</body>';

    $this->registerTestingTemplate('/badhtml/contententity_quot.html', $template);
    $page = $this->initTemplate('/badhtml/contententity_quot.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testContentEntity_amp()
  {
    $template = '<body>&</body>';

    $this->registerTestingTemplate('/badhtml/contententity_amp.html', $template);
    $page = $this->initTemplate('/badhtml/contententity_amp.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function XtestEmptyClose()
  {
    $template = '<body></></body>';

    $this->registerTestingTemplate('/badhtml/emptyclose.html', $template);
    $page = $this->initTemplate('/badhtml/emptyclose.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function XtestEmptyOpen()
  {
    $template = '<body><></body>';

    $this->registerTestingTemplate('/badhtml/emptyopen.html', $template);
    $page = $this->initTemplate('/badhtml/emptyopen.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testDoubleClosingTagProcessingAsASingleString()
  {
    $template = '<html><body></body/></html>';

    $this->registerTestingTemplate('/badhtml/doubleclose.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/doubleclose.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Lonely closing tag/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), 'body/');
    }
  }

  function testClosingTagWithAttributeProcessedAsASingleString()
  {
    $template = '<body></body attribute="test">';

    $this->registerTestingTemplate('/badhtml/closeattribute.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/closeattribute.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Lonely closing tag/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), 'body attribute="test"');
    }
  }

  function testTruncatedTag1()
  {
    $template = '<';

    $this->registerTestingTemplate('/badhtml/trunc_tag1.html', $template);
    $page = $this->initTemplate('/badhtml/trunc_tag1.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testTruncatedTag2()
  {
    $template = '<bo';

    $this->registerTestingTemplate('/badhtml/trunc_tag2.html', $template);
    $page = $this->initTemplate('/badhtml/trunc_tag2.html');
    $output = $page->capture();
    $this->assertEqual($output, "<bo");
  }

  function testTruncatedClose1()
  {
    $template = '<body color="#999999">hello</';

    $this->registerTestingTemplate('/badhtml/trunc_close1.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/trunc_close1.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
    }
  }

  function testTruncatedClose2()
  {
    $template = '<body color="#999999">hello</body';

    $this->registerTestingTemplate('/badhtml/trunc_close2.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/trunc_close2.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
    }
  }

  function testTruncatedValue()
  {
    $template = '<body color="#99';

    $this->registerTestingTemplate('/badhtml/trunc_value.html', $template);
    $page = $this->initTemplate('/badhtml/trunc_value.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body color="#99');
  }

  function testWactAttributeEntity_lt()
  {
    $template = '<body><core:block id="test" attribute="<">contents</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_attributeentity_lt.html', $template);
    $page = $this->initTemplate('/badhtml/wact_attributeentity_lt.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>contents</body>');
  }

  function testWactAttributeEntity_gt()
  {
    $template = '<body><core:block id="test" attribute=">">contents</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_attributeentity_gt.html', $template);
    $page = $this->initTemplate('/badhtml/wact_attributeentity_gt.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>contents</body>');
  }

  function testWactAttributeEntity_quot()
  {
    $template = '<body><core:block id="test" attribute="\'test\'">contents</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_attributeentity_quot.html', $template);
    $page = $this->initTemplate('/badhtml/wact_attributeentity_quot.html');
    $output = $page->capture();

    $this->assertEqual($output, '<body>contents</body>');
  }

  function testWactAttributeEntity_amp()
  {
    $template = '<body><core:block id="test" attribute="&">contents</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_attributeentity_amp.html', $template);
    $page = $this->initTemplate('/badhtml/wact_attributeentity_amp.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>contents</body>');
  }

  /*
  // Bad test
  function testWactContentEntity_lt()
  {
    $template = '<body><core:block id="test"><</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_contententity_lt.html', $template);
    $page = $this->initTemplate('/badhtml/wact_contententity_lt.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body><</body>');
  }
  */

  function testWactContentEntity_gt()
  {
    $template = '<body><core:block id="test">></core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_contententity_gt.html', $template);
    $page = $this->initTemplate('/badhtml/wact_contententity_gt.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>></body>');
  }

  function testWactContentEntity_quot()
  {
    $template = '<body><core:block id="test">"</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_contententity_quot.html', $template);
    $page = $this->initTemplate('/badhtml/wact_contententity_quot.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>"</body>');
  }

  function testWactContentEntity_amp()
  {
    $template = '<body><core:block id="test">&</core:block></body>';

    $this->registerTestingTemplate('/badhtml/wact_contententity_amp.html', $template);
    $page = $this->initTemplate('/badhtml/wact_contententity_amp.html');
    $output = $page->capture();
    $this->assertEqual($output, '<body>&</body>');
  }

  function testWactTruncatedClose1()
  {
    $template = '<body><core:block id="test">Content</';

    $this->registerTestingTemplate('/badhtml/wact_trunc_close1.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_close1.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactTruncatedClose2()
  {
    $template = '<body><core:block id="test">Content</core:block';

    $this->registerTestingTemplate('/badhtml/wact_trunc_close2.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_close2.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactTruncatedClose3()
  {
    $template = '<body><core:block id="test">Content</core:blo';

    $this->registerTestingTemplate('/badhtml/wact_trunc_close3.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_close3.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactTruncatedValue()
  {
    $template = '<body><core:block id="test';

    $this->registerTestingTemplate('/badhtml/wact_trunc_value.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_value.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
    }
  }

  function testWactTruncatedContent()
  {
    $template = '<body><core:block id="test">content';

    $this->registerTestingTemplate('/badhtml/wact_trunc_content.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_content.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testTruncatedAttribute()
  {
    $template = '<body col';

    $this->registerTestingTemplate('/badhtml/trunc_attribute.html', $template);
    $page = $this->initTemplate('/badhtml/trunc_attribute.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testWactTruncatedAttribute()
  {
    $template = '<body><core:block id';

    $this->registerTestingTemplate('/badhtml/wact_trunc_attribute.html', $template);
    try
    {
      $page = $this->initTemplate('/badhtml/wact_trunc_attribute.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing close tag/', $e->getMessage());
    }
  }
}

