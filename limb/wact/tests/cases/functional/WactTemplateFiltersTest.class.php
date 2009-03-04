<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */


class WactTemplateFiltersTest extends WactTemplateTestCase
{
  function testStringConstant()
  {
    $template = '{$"hello"|uppercase}';

    $this->registerTestingTemplate('/template/filter/string.html', $template);
    $page = $this->initTemplate('/template/filter/string.html');
    $output = $page->capture();
    $this->assertEqual($output, 'HELLO');
  }

  function testWactAttributeVariableFilter()
  {
    $template = '<form id="test" extra="{$Var|uppercase}" runat="server">contents</form>';

    $this->registerTestingTemplate('/template/filter/wactattributevarfilter.html', $template);
    $page = $this->initTemplate('/template/filter/wactattributevarfilter.html');

    $form = $page->getChild('test');
    $data = new ArrayObject(array('Var' => 'Foo'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="FOO">contents</form>');
  }

  function testFilter()
  {
    $template = '{$Var|uppercase}';

    $this->registerTestingTemplate('/template/filter/filter.html', $template);
    $page = $this->initTemplate('/template/filter/filter.html');
    $page->set('Var', 'Foo');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }

  function testFilterChain()
  {
    $template = '{$Var|trim|uppercase}';

    $this->registerTestingTemplate('/template/filter/filterchain.html', $template);
    $page = $this->initTemplate('/template/filter/filterchain.html');
    $page->set('Var', '   Foo   ');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }

  function testFilterChainOrder()
  {
    $template = '{$Var|lowercase|uppercase}';

    $this->registerTestingTemplate('/template/filter/filterchainorder.html', $template);

    $page = $this->initTemplate('/template/filter/filterchainorder.html');
    $page->set('Var', 'Hello');

    $output = $page->capture();
    $this->assertEqual($output, 'HELLO');
  }

  function testVarSetFilterChain()
  {
    $template = '<core:set var2="{$Var|uppercase}"/>{$var2|trim}';

    $this->registerTestingTemplate('/template/filter/varfiltersetchain.html', $template);
    $page = $this->initTemplate('/template/filter/varfiltersetchain.html');
    $page->set('Var', '   Foo   ');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }

  function testSetFilterChain()
  {
    $template = '<core:set var1="   Foo   "/><core:set var2="{$var1|uppercase}"/>{$var2|trim}';

    $this->registerTestingTemplate('/template/filter/filtersetchain.html', $template);
    $page = $this->initTemplate('/template/filter/filtersetchain.html');

    $output = $page->capture();
    $this->assertEqual($output, 'FOO');
  }

  function testFilterParameter()
  {
    $template = '{$Var|wordwrap:10}';

    $this->registerTestingTemplate('/template/filter/parameter.html', $template);
    $page = $this->initTemplate('/template/filter/parameter.html');
    $page->set('Var', 'The quick brown fox jumped over the lazy dog.');

    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }

  function testFilterVariableParameter()
  {
    $template = '{$Var|wordwrap:Size}';

    $this->registerTestingTemplate('/template/filter/varparameter.html', $template);
    $page = $this->initTemplate('/template/filter/varparameter.html');
    $page->set('Var', 'The quick brown fox jumped over the lazy dog.');
    $page->set('Size', 10);

    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }

  function testFilterParameterExpression()
  {
    $template = '{$Var|wordwrap:5*2}';

    $this->registerTestingTemplate('/template/filter/parameter_expression.html', $template);
    $page = $this->initTemplate('/template/filter/parameter_expression.html');
    $page->set('Var', 'The quick brown fox jumped over the lazy dog.');

    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }

  function testFilterParameterComplexExpression()
  {
    $template = '{$Var|wordwrap:3*3 + Size}';

    $this->registerTestingTemplate('/template/filter/parameter_complex_expression.html', $template);
    $page = $this->initTemplate('/template/filter/parameter_complex_expression.html');
    $page->set('Var', 'The quick brown fox jumped over the lazy dog.');
    $page->set('Size', 1);

    $output = $page->capture();
    $this->assertEqual($output, "The quick\nbrown fox\njumped\nover the\nlazy dog.");
  }

  function testTooManyFilterParameters()
  {
    $template = '{$Var|uppercase:80}';

    $this->registerTestingTemplate('/template/filter/toomany-parameters.html', $template);
    try
    {
      $page = $this->initTemplate('/template/filter/toomany-parameters.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testParameterParsing()
  {
    $template = '{$Var | default : "|trim"}';

    $this->registerTestingTemplate('/template/filter/parameterparsing.html', $template);
    $page = $this->initTemplate('/template/filter/parameterparsing.html');
    $output = $page->capture();
    $this->assertEqual($output, "|trim");
  }

  function testParameterParsing2()
  {
    $template = '{$Var | default : "test: 99"}';

    $this->registerTestingTemplate('/template/filter/parameterparsing2.html', $template);
    $page = $this->initTemplate('/template/filter/parameterparsing2.html');
    $output = $page->capture();
    $this->assertEqual($output, "test: 99");
  }

  function testParameterParsing3()
  {
    $template = '{$Var | default : ", test: 99"}';

    $this->registerTestingTemplate('/template/filter/parameterparsing3.html', $template);
    $page = $this->initTemplate('/template/filter/parameterparsing3.html');
    $output = $page->capture();
    $this->assertEqual($output, ", test: 99");
  }

  function testNumberFormatFilter()
  {
    $template = '{$Var|number} {$Var|number:2} {$Var|number:3, ",", "."}';

    $this->registerTestingTemplate('/template/filter/number_format_filter.html', $template);
    $page = $this->initTemplate('/template/filter/number_format_filter.html');
    $page->set('Var', 1234567.4321);

    $output = $page->capture();
    $this->assertEqual($output, '1,234,567 1,234,567.43 1.234.567,432');
  }

  function testManualHtmlEscape()
  {
    $template = '{$Var|html}';

    $this->registerTestingTemplate('/template/filter/manual_html_escape.html', $template);
    $page = $this->initTemplate('/template/filter/manual_html_escape.html');
    $page->set('Var', '<tag>');

    $output = $page->capture();
    $this->assertEqual($output, '&lt;tag&gt;');
  }

  function testHexConstant()
  {
    $template = '{$"hello"|hex|raw}';
    $this->registerTestingTemplate('/template/filter/hex_string.html', $template);

    $page = $this->initTemplate('/template/filter/hex_string.html');
    $output = $page->capture();
    $this->assertEqual($output, '&#x68;&#x65;&#x6c;&#x6c;&#x6f;');
  }

  function testHexVariable()
  {
    $template = '{$email|hex|raw}';
    $this->registerTestingTemplate('/template/filter/hex_email.html', $template);

    $page = $this->initTemplate('/template/filter/hex_email.html');
    $page->set('email', 'user@domain.com');
    $output = $page->capture();
    $this->assertEqual($output, '&#x75;&#x73;&#x65;&#x72;&#x40;&#x64;&#x6f;&#x6d;&#x61;&#x69;&#x6e;&#x2e;&#x63;&#x6f;&#x6d;');
  }
}

