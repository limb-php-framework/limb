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
class WactTagAttributesTest extends WactTemplateTestCase
{
  function testTextAttribute()
  {
    $template = '<P ALIGN="CENTER"></P>';

    $this->registerTestingTemplate('/attributes/text_node_attribute.html', $template);
    $page = $this->initTemplate('/attributes/text_node_attribute.html');

    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testWactAttribute()
  {
    $template = '<form id="test" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_attribute.html', $template);
    $page = $this->initTemplate('/attributes/wact_attribute.html');

    $form = $page->getChild('test');
    $form->setAttribute('extra', 'Foo');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="Foo">contents</form>');
  }

  function testWactAttributeVariable()
  {
    $template = '<form id="test" extra="{$Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_attribute_var.html', $template);
    $page = $this->initTemplate('/attributes/wact_attribute_var.html');

    $form = $page->getChild('test');

    $data = new ArrayObject();
    $data['Var'] = 'Foo';
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="Foo">contents</form>');
  }

  function testWactAttributeSummExpression()
  {
    $template = '<form id="test" extra="{$2 + 2}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_attribute_summ_expression.html', $template);
    $page = $this->initTemplate('/attributes/wact_attribute_summ_expression.html');

    $form = $page->getChild('test');

    $data = new ArrayObject();
    $data['Var'] = 'Foo';
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="4">contents</form>');
  }


  function testWactWactAttributeVariable()
  {
    $template = '<form id="test" extra="bar{$Var}bar" extra2="{$Var}bar{$Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_compound_attribute_var.html', $template);
    $page = $this->initTemplate('/attributes/wact_compound_attribute_var.html');

    $form = $page->getChild('test');

    $data = new ArrayObject(array('Var' => 'Foo'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="barFoobar" extra2="FoobarFoo">contents</form>');
  }

  function testWactWactAttributeExpression()
  {
    $template = '<form id="test" extra="bar{$3*3 + 1}bar" extra2="{$Var}bar{$Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_compound_attribute_expression.html', $template);
    $page = $this->initTemplate('/attributes/wact_compound_attribute_expression.html');

    $form = $page->getChild('test');

    $data = new ArrayObject(array('Var' => 'Foo'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="bar10bar" extra2="FoobarFoo">contents</form>');
  }

  function testWactWactAttributeExpressionWithDBE()
  {
    $template = '<form id="test" extra="bar{$3*3 + var}bar" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_compound_attribute_expression_with_dbe.html', $template);
    $page = $this->initTemplate('/attributes/wact_compound_attribute_expression_with_dbe.html');

    $form = $page->getChild('test');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="bar9bar">contents</form>');
  }

  function testWactAttributeVariableFilter()
  {
    $template = '<form id="test" extra="{$Var|uppercase}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wact_attribute_var_filter.html', $template);
    $page = $this->initTemplate('/attributes/wact_attribute_var_filter.html');

    $form = $page->getChild('test');

    $data = new ArrayObject(array('Var' => 'Foo'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="FOO">contents</form>');
  }

  function testSetAttributeVariable()
  {
    $template = '<core:SET Var="bar"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/set_attribute_var.html', $template);
    $page = $this->initTemplate('/attributes/set_attribute_var.html');

    $form = $page->getChild('test');

    $data = new ArrayObject(array('Var' => 'Foo'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="bar">contents</form>');
  }

  function testWactAttributeEscape()
  {
    $template = '<form id="test" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wactattributeescape.html', $template);
    $page = $this->initTemplate('/attributes/wactattributeescape.html');

    $form = $page->getChild('test');
    $form->setAttribute('extra', '&"\'<>');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&amp;&quot;&#039;&lt;&gt;">contents</form>');
  }

  function testWactAttributeVariableEscape()
  {
    $template = '<form id="test" extra="{$Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/wactattributevarescape.html', $template);
    $page = $this->initTemplate('/attributes/wactattributevarescape.html');

    $form = $page->getChild('test');

    $data = new ArrayObject(array('Var' => '&"\'<>'));
    $form->registerDataSource($data);

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&amp;&quot;&#039;&lt;&gt;">contents</form>');
  }

  function testSetAttributeVariableEscape()
  {
    $template = '<core:SET Var="&quot;"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescape.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescape.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&quot;">contents</form>');
  }

  function testSetAttributeVariableEscapeLt()
  {
    $template = '<core:SET Var="<"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescapelt.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescapelt.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&lt;">contents</form>');
  }

  function testSetAttributeVariableEscapeGt()
  {
    $template = '<core:SET Var=">"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescapegt.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescapegt.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&gt;">contents</form>');
  }

  function testSetAttributeVariableEscapeAmp()
  {
    $template = '<core:SET Var="&"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescapeamp.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescapeamp.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&amp;">contents</form>');
  }

  function testSetAttributeVariableEscapeQuot()
  {
    $template = '<core:SET Var=\'"\'/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescapequot.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescapequot.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&quot;">contents</form>');
  }

  function testSetAttributeVariableEscapeSingleQuot()
  {
    $template = '<core:SET Var="\'"/><form id="test" extra="{$^Var}" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/setattributevarescapesinglequot.html', $template);
    $page = $this->initTemplate('/attributes/setattributevarescapesinglequot.html');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&#039;">contents</form>');
  }

  function testAttributeVariableEscape()
  {
    $template = '<form id="test" extra="&quot;&lt;&gt;&amp;" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescape.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescape.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), '"<>&');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&quot;&lt;&gt;&amp;">contents</form>');
  }

  function testAttributeVariableEscapeLt()
  {
    $template = '<form id="test" extra="<" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescapelt.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescapelt.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), '<');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&lt;">contents</form>');
  }

  function testAttributeVariableEscapeGt()
  {
    $template = '<form id="test" extra=">" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescapegt.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescapegt.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), '>');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&gt;">contents</form>');
  }

  function testAttributeVariableEscapeAmp()
  {
    $template = '<form id="test" extra="&" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescapeamp.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescapeamp.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), '&');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&amp;">contents</form>');
  }

  function testAttributeVariableEscapeQuot()
  {
    $template = '<form id="test" extra=\'"\' runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescapequot.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescapequot.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), '"');

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&quot;">contents</form>');
  }

  function testAttributeVariableEscapeSingleQuot()
  {
    $template = '<form id="test" extra="\'" runat="server">contents</form>';

    $this->registerTestingTemplate('/attributes/attributevarescapesinglequot.html', $template);
    $page = $this->initTemplate('/attributes/attributevarescapesinglequot.html');

    $form = $page->getChild('test');
    $this->assertEqual($form->getAttribute('extra'), "'");

    $output = $page->capture();
    $this->assertEqual($output, '<form id="test" extra="&#039;">contents</form>');
  }

  function testNullAttributeValue()
  {
    $template = '<P id="test" runat="server" extra>contents</P>';

    $this->registerTestingTemplate('/attributes/minimized_attribute.html', $template);

    $page = $this->initTemplate('/attributes/minimized_attribute.html');
    $this->assertEqual($page->capture(), '<P id="test" extra>contents</P>');
  }

  function testEmptyAttributeValue()
  {
    $template = '<P id="test" extra="">contents</P>';

    $this->registerTestingTemplate('/attributes/empty_attribute_value.html', $template);

    $page = $this->initTemplate('/attributes/empty_attribute_value.html');

    $this->assertEqual($page->capture(), '<P id="test" extra="">contents</P>');
  }

  function testJavaScriptAttribute()
  {
    $template = '<P onmouseover="window.status=\'Test\'; return true">contents</P>';

    $this->registerTestingTemplate('/attributes/javascriptattribute.html', $template);
    $page = $this->initTemplate('/attributes/javascriptattribute.html');

    $output = $page->capture();
    $this->assertEqual($output, $template);
  }

  function testJavaScriptWactAttribute()
  {
    $template = '<P id="test" runat="server" onmouseover="window.status=\'Test\'; return true">contents</P>';

    $this->registerTestingTemplate('/attributes/javascriptwactattribute.html', $template);
    $page = $this->initTemplate('/attributes/javascriptwactattribute.html');

    $output = $page->capture();
    $this->assertEqual($output, '<P id="test" onmouseover="' . htmlspecialchars('window.status=\'Test\'', ENT_QUOTES) . '; return true">contents</P>');
  }

/*  This test case is fatal.  commented out so the test run can continue
  function testWactAttributeVariableId() {
      $template = '<form id="test" runat="server" id="{$var}">contents</form>';

      $this->registerTestingTemplate('/attributes/wactattributevarid.html', $template);
      $page = $this->initTemplate('/attributes/wactattributevarid.html');
      $output = $page->capture();

      $this->assertError(); // ILLEGALVARREFINATTR
      $this->swallowErrors();
  }
*/

  function testWactAttributeVariableRunat()
  {
    $template = '<form id="test" runat="{$var}">contents</form>';

    $this->registerTestingTemplate('/attributes/wactattributevarrunat.html', $template);

    try
    {
      $page = $this->initTemplate('/attributes/wactattributevarrunat.html');
      $output = $page->capture();
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testWactAttributeVariableRunatSelfClose()
  {
    $template = '<br runat="{$var}"/>';

    $this->registerTestingTemplate('/attributes/wactattributevarrunatselfclose.html', $template);
    try
    {
      $page = $this->initTemplate('/attributes/wactattributevarrunatselfclose.html');
      $output = $page->capture();
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }
}

