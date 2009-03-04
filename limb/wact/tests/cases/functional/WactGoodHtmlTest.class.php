<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* This tests the parsing of HTML constructs that are considered valid HTML, but that
* are not considered valid XML.  This tests the ability of the template parser to
* work with HTML edge cases.
*/
class WactGoodHtmlTest extends WactTemplateTestCase
{
  function testSelfClose()
  {
    $template = '<BR /><BR />\n<BR />\n<B></B><BR />\n';

    $this->registerTestingTemplate('/goodhtml/selfclose.html', $template);
    $page = $this->initTemplate('/goodhtml/selfclose.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testWactSelfCloseForbidden()
  {
    $template = '<core:SET Variable="Value" />';

    $this->registerTestingTemplate('/goodhtml/wactselfcloseforbid.html', $template);
    $page = $this->initTemplate('/goodhtml/wactselfcloseforbid.html');
    $output = $page->capture();
    $this->assertEqual($output, "");
  }

  function testUnclosedHtmlTagInCoreOptional()
  {
    $template = '<core:optional for="{$var}"><tr></core:optional>';
    $this->registerTestingTemplate('/goodhtml/unclosed_html_tag_in_core_optional.html', $template);
    $page = $this->initTemplate('/goodhtml/unclosed_html_tag_in_core_optional.html');
    $page->set('var', 1);
    $output = $page->capture();
    $this->assertEqual($output, '<tr>');
  }

  function testMinimizedAttribute()
  {
    $template = '<TAG CHECKED /><TAG CHECKED="TRUE" /><TAG CHECKED="FALSE" />';
    $this->registerTestingTemplate('/goodhtml/minimizedattribute.html', $template);

    $page = $this->initTemplate('/goodhtml/minimizedattribute.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testEscapeWhitespace()
  {
    $this->registerTestingTemplate('/goodhtml/escapewhitespace.html',
        "<body>\n" . '{$test}' . "\n</body>");

    $page = $this->initTemplate('/goodhtml/escapewhitespace.html');
    $output = $page->capture();
    $this->assertEqual($output, "<body>\n\n</body>");
  }

  function testXMLEscapes()
  {
    $template = <<<TEMPLATE
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
      'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>

    <title>XML escapes</title>

  </head>
  <body>

    <!-- A comment -->

    <!--DOCTYPE as first string in comment -->

    <!-- a legal comment containing a <tag> and an &entity_reference; -->

    <script type="text/javascript" xml:space="preserve">
      <![CDATA[
      string = 'A CDATA block';
      ]]>
    </script>

    <h1>XML escapes</h1>

  </body>
</html>
TEMPLATE;

    $this->registerTestingTemplate('/goodhtml/xmlescapes.html', $template);

    $page = $this->initTemplate('/goodhtml/xmlescapes.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testJavaScriptCommentEmbed()
  {
    $template = <<<TEMPLATE
<script language="Javascript">
<!--
document.write('<B>Test<\/B>');
//-->
</script>
TEMPLATE;

    $this->registerTestingTemplate('/goodhtml/javascript-comment.html', $template);

    $page = $this->initTemplate('/goodhtml/javascript-comment.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testJavaScriptWithDBE()
  {
    $template = '<script language="Javascript">a = "{$var}"<!--document.write("<B>Test<\/B>");//--></script>';

    $extected = '<script language="Javascript">a = "test"<!--document.write("<B>Test<\/B>");//--></script>';

    $this->registerTestingTemplate('/goodhtml/javascriptwithdbe.html', $template);

    $page = $this->initTemplate('/goodhtml/javascriptwithdbe.html');
    $page->set('var', 'test');
    $output = $page->capture();
    $this->assertEqual($output, $extected);
  }

  function testWactTagsWithinCDATA()
  {
    $template = '<script type="text/javascript">//<![CDATA['.
                '<list:list id="items"><list:item>{$title}|</list:item></list:list>'.
                '//]]></script>';

    $this->registerTestingTemplate('/goodhtml/javascript_with_wact_tags_in_cdata.html', $template);

    $page = $this->initTemplate('/goodhtml/javascript_with_wact_tags_in_cdata.html');
    $page->setChildDataset('items', array(array('title' => 'first'),
                                          array('title' => 'second')));
    $expected = '<script type="text/javascript">//<![CDATA[first|second|//]]></script>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testJavaScriptComplexCommentEmbed()
  {
    $template = <<<TEMPLATE
<script language="Javascript">
<!--
document.write('<A HREF="http://localhost/>Test<\/A>');
//-->
</script>
TEMPLATE;

    $this->registerTestingTemplate('/goodhtml/javascriptcomplex-comment.html', $template);

    $page = $this->initTemplate('/goodhtml/javascriptcomplex-comment.html');
    $output = $page->capture();
    $this->assertEqual($output, $template);
  }
}

