<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreSetTagTest.class.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
 */

class WactCoreSetTagTest extends WactTemplateTestCase
{
   function testEmptyReference()
   {
     $template = '{$Missing}';
     $this->registerTestingTemplate('/tags/core/empty_reference.html', $template);

     $page = $this->initTemplate('/tags/core/empty_reference.html');
     $output = $page->capture();
     $this->assertEqual($output, '');
   }

   function testNormalReference()
   {
     $template = '{$Missing}';
     $this->registerTestingTemplate('/tags/core/normal_reference.html', $template);

     $page = $this->initTemplate('/tags/core/normal_reference.html');
     $page->set('Missing', 'not');
     $output = $page->capture();
     $this->assertEqual($output, 'not');
   }

   function testEmptySet()
   {
     $template = '<core:SET/>';
     $this->registerTestingTemplate('/tags/core/empty_set.html', $template);

     $page = $this->initTemplate('/tags/core/empty_set.html');
     $output = $page->capture();
     $this->assertEqual($output, '');
   }

   function testEndTagForbidden()
   {
     $template = '<core:SET></core:SET>';
     $this->registerTestingTemplate('/tags/core/end_tag_forbidden.html', $template);

     try
     {
       $page = $this->initTemplate('/tags/core/end_tag_forbidden.html');
     }
     catch(WactException $e)
     {
       $this->assertWantedPattern('/Closing tag is forbidden for this tag. Use self closing notation/', $e->getMessage());
     }
   }

   function testSet()
   {
     $template = '<core:SET var="value"/>';
     $this->registerTestingTemplate('/tags/core/set.html', $template);

     $page = $this->initTemplate('/tags/core/set.html');
     $output = $page->capture();
     $this->assertEqual($output, '');
   }

   function testSetAndReference()
   {
     $template = '<core:SET Var="value"/>{$Var}';
     $this->registerTestingTemplate('/tags/core/set_and_reference.html', $template);

     $page = $this->initTemplate('/tags/core/set_and_reference.html');
     $output = $page->capture();
     $this->assertEqual($output, 'value');
   }

   function testSetFilterAndReference()
   {
     $template = '<core:SET Var="value"/>{$Var|uppercase}';
     $this->registerTestingTemplate('/tags/core/set_filter_and_reference.html', $template);

     $page = $this->initTemplate('/tags/core/set_filter_and_reference.html');
     $output = $page->capture();
     $this->assertEqual($output, 'VALUE');
   }

   function testReferenceChain()
   {
     $template = '<core:SET First="val"/><core:SET Second="{$First}"/>{$First}-{$Second}';
     $this->registerTestingTemplate('/tags/core/reference_chain.html', $template);

     $page = $this->initTemplate('/tags/core/reference_chain.html');
     $output = $page->capture();
     $this->assertEqual($output, 'val-val');
   }

   function testSetPrecidence()
   {
     $template = '<core:SET Var="value"/>{$Var}';
     $this->registerTestingTemplate('/tags/core/set_precidence.html', $template);

     $page = $this->initTemplate('/tags/core/set_precidence.html');
     $page->set('Var', 'different value');
     $output = $page->capture();
     $this->assertEqual($output, 'value');
   }

   function testSetWithFilter()
   {
     $template = '<core:SET Var="{$orig|uppercase}"/>{$Var}';
     $this->registerTestingTemplate('/tags/core/set_with_filter.html', $template);

     $page = $this->initTemplate('/tags/core/set_with_filter.html');
     $page->set('orig', 'foo');
     $output = $page->capture();
     $this->assertEqual($output, 'FOO');
   }

   function testSetWithFilterSingleQuotes()
   {
     $template = '<core:SET Var=\'{$orig|uppercase}\'/>{$Var}';
     $this->registerTestingTemplate('/tags/core/set_with_filter2.html', $template);

     $page = $this->initTemplate('/tags/core/set_with_filter2.html');
     $page->set('orig', 'foo');
     $output = $page->capture();
     $this->assertEqual($output, 'FOO');
   }

   function testSetMultiValues()
   {
     $template = '<core:SET Var1="{$orig}" Var2="aaa"/>{$Var1}-{$Var2}';
     $this->registerTestingTemplate('/tags/core/set_multi.html', $template);

     $page = $this->initTemplate('/tags/core/set_multi.html');
     $page->set('orig', 'foo');
     $output = $page->capture();
     $this->assertEqual($output, 'foo-aaa');
   }

   function testValueIsAvailableAtRunTimeStaticBinding()
   {
     $template = '<core:DATASOURCE id="test"><core:SET var1="value1"/>{$var1}</core:DATASOURCE>';
     $this->registerTestingTemplate('/tags/core/set_value_is_available_at_runtime.html', $template);

     $page = $this->initTemplate('/tags/core/set_value_is_available_at_runtime.html');
     $datasource = $page->findChild('test');
     $output = $page->capture();
     $this->assertEqual($output, 'value1');
     $this->assertEqual($datasource->get('var1'), 'value1');
   }

  function testValueIsAvailableAtRunTimeDynamicBinding()
  {
    $template = '<core:DATASOURCE id="test"><core:SET var1="{$#value1|uppercase}"/>{$var1}</core:DATASOURCE>';
    $this->registerTestingTemplate('/tags/core/set_value_is_available_at_runtime2.html', $template);

    $page = $this->initTemplate('/tags/core/set_value_is_available_at_runtime2.html');
    $page->set('value1', 'some_value');
    $datasource = $page->findChild('test');
    $output = $page->capture();
    $this->assertEqual($output, 'SOME_VALUE');
    $this->assertEqual($datasource->get('var1'), 'SOME_VALUE');
  }

  function testValueIsSetIntoCurrentDataspaceNotJustCurrentComponent()
  {
    $template = '<core:DATASOURCE id="test"><div wact:id="any"><core:SET var1="5"/>{$var1}</div></core:DATASOURCE>';
    $this->registerTestingTemplate('/tags/core/set_value_in_datasource_not_component.html', $template);

    $page = $this->initTemplate('/tags/core/set_value_in_datasource_not_component.html');
    $this->assertEqual($page->capture(), '<div>5</div>');
  }

  function testOnlyGenerateRuntimeExpression()
  {
    $template = '<core:optional for="item1"><core:SET color="yellow" runtime="true"/></core:optional>'.
                '<core:optional for="item2"><core:SET color="green" runtime="true"/></core:optional>'.
                '{$color}';
    $this->registerTestingTemplate('/tags/core/set_value_in_runtime_only.html', $template);

    $page = $this->initTemplate('/tags/core/set_value_in_runtime_only.html');
    $page->set('item1', true);
    $this->assertEqual($page->capture(), 'yellow');
  }
}
?>
