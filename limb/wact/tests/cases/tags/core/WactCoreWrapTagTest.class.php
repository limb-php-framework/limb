<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactCoreWrapTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $Wrapper = '<B><core:placeholder id="boldarea"/></B>';
    $this->registerTestingTemplate('/tags/core/wrap/bold_wrapper.html', $Wrapper, 'bold_wrapper.html');

    $Wrapper = '<div wact:id="content">some content</div>';
    $this->registerTestingTemplate('/tags/core/wrap/wrapper_with_content.html', $Wrapper, 'wrapper_with_content.html');

    $Wrapper = '<I><core:placeholder id="italicarea"/></I>';
    $this->registerTestingTemplate('/tags/core/wrap/italic_wrapper.html', $Wrapper, 'italic_wrapper.html');

    $Wrapper = '<B><core:placeholder id="boldarea"/></B><I><core:placeholder id="italicarea"/></I>';
    $this->registerTestingTemplate('/tags/core/wrap/two_placeholders_wrapper.html', $Wrapper, 'two_placeholders_wrapper.html');
  }

  function testSimpleWrap()
  {
    $template = '<core:wrap file="bold_wrapper.html" insertat="boldarea">Hello</core:wrap>';
    $this->registerTestingTemplate('/tags/core/wrap/simple_wrap.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/simple_wrap.html');
    $output = $page->capture();
    $this->assertEqual($output, '<B>Hello</B>');
  }

  function testWrapIntoRegularDivTagUsingInAttrThatPrependsContent()
  {
    $template = '<core:wrap file="wrapper_with_content.html" in="content"> Hello </core:wrap>';
    $this->registerTestingTemplate('/tags/core/wrap/wrap_with_in_attr.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/wrap_with_in_attr.html');
    $output = $page->capture();
    $this->assertEqual($output, '<div>some content Hello </div>');
  }

  function testWrapIntoRegularDivTagUsingAsAttrThatWillReplaceContent()
  {
    $template = '<core:wrap file="wrapper_with_content.html" as="content"> Hello </core:wrap>';
    $this->registerTestingTemplate('/tags/core/wrap/wrap_with_as_attr.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/wrap_with_as_attr.html');
    $output = $page->capture();
    $this->assertEqual($output, '<div> Hello </div>');
  }

  function testNoErrorToUseSamePlaceholderWrapTwice()
  {
    $template =
        '<core:wrap file="bold_wrapper.html" insertat="boldarea">' .
        '<core:wrap file="bold_wrapper.html" insertat="boldarea">Hello</core:wrap></core:wrap>';
    $this->registerTestingTemplate('/tags/core/wrap/same_placeholder_wrap.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/same_placeholder_wrap.html');
    $output = $page->capture();
    $this->assertEqual($output, '<B><B>Hello</B></B>');
  }

  function testDifferentPlaceholderWrapTwice()
  {
    $template =
        '<core:wrap file="bold_wrapper.html" insertat="boldarea">' .
        '<core:wrap file="italic_wrapper.html" insertat="italicarea">Hello</core:wrap></core:wrap>';

    $this->registerTestingTemplate('/tags/core/wrap/diff_placeholder_wrap.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/diff_placeholder_wrap.html');
    $output = $page->capture();
    $this->assertEqual($output, '<B><I>Hello</I></B>');
  }

  function testInsertInfoDifferentPlaceholdersOfTheSameFile()
  {
    $template =
        '<core:wrap file="two_placeholders_wrapper.html">'.
          '<core:wrap insertat="boldarea">First</core:wrap>'.
          '<core:wrap insertat="italicarea">Second</core:wrap>'.
        '</core:wrap>';

    $this->registerTestingTemplate('/tags/core/wrap/diff_placeholders_same_file.html', $template);

    $page = $this->initTemplate('/tags/core/wrap/diff_placeholders_same_file.html');
    $output = $page->capture();
    $this->assertEqual($output, '<B>First</B><I>Second</I>');
  }

  function testThrowExceptionWithoutFileAtParent()
  {
    $template =
        '<core:wrap>'.
          '<core:wrap insertat="boldarea">First</core:wrap>'.
          '<core:wrap insertat="italicarea">Second</core:wrap>'.
        '</core:wrap>';

    $this->registerTestingTemplate('/tags/core/wrap/diff_placeholders_same_wrap_no_file_attr.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/wrap/diff_placeholders_same_wrap_no_file_attr.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
    }
  }

  function testThrowExceptionWithoutInsertAtChild()
  {
    $template =
        '<core:wrap file="two_placeholders_wrapper.html">'.
          '<core:wrap>First</core:wrap>'.
          '<core:wrap insertat="italicarea">Second</core:wrap>'.
        '</core:wrap>';

    $this->registerTestingTemplate('/tags/core/wrap/diff_placeholders_same_wrap_no_insertat_attr.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/core/wrap/diff_placeholders_same_wrap_no_insertat_attr.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
    }
  }
}

