<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactCoreDatasourceTagTest extends WactTemplateTestCase
{
  function testDatasource()
  {
    $template = '<core:datasource id="middle">{$Var}:{$^Var}:{$#Var}</core:datasource>';
    $this->registerTestingTemplate('/tags/core/dataspace/dataspace.html', $template);

    $page = $this->initTemplate('/tags/core/dataspace/dataspace.html');
    $page->set('Var', 'outer');

    $middle = $page->getChild('middle');
    $middle->set('Var', 'middle');

    $output = $page->capture();
    $this->assertEqual($output, 'middle:outer:outer');
  }

  function testNestedDatasource()
  {
    $template = '{$Var}:{$#Var}' .
                '<core:datasource id="middle">' .
                  '-{$Var}:{$^Var}:{$#Var}' .
                  '<core:datasource id="inner">-{$Var}:{$^Var}:{$#Var}</core:datasource>' .
                '</core:datasource>';
    $this->registerTestingTemplate('/tags/core/dataspace/nesteddataspace.html', $template);

    $page = $this->initTemplate('/tags/core/dataspace/nesteddataspace.html');
    $page->set('Var', 'outer');

    $middle = $page->getChild('middle');
    $middle->set('Var', 'middle');

    $inner = $page->getChild('inner');
    $inner->set('Var', 'inner');

    $output = $page->capture();
    $this->assertEqual($output, 'outer:outer-middle:outer:outer-inner:middle:outer');
  }

  function testSetDatasource()
  {
    $template = '<core:SET Var="outer"/>'.
                 '<core:datasource id="middle">' .
                  '<core:SET Var="middle"/>'.
                  '{$Var}:{$^Var}:{$#Var}' .
                 '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/set_dataspace.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/set_dataspace.html');
    $output = $page->capture();
    $this->assertEqual($output, 'middle:outer:outer');
  }

  function testSetNestedDatasource()
  {
    $template =
        '<core:SET Var="outer"/>'.
        '{$Var}:{$#Var}' .
        '<core:datasource id="middle">' .
        '<core:SET Var="middle"/>'.
        '-{$Var}:{$^Var}:{$#Var}' .
        '<core:datasource id="inner">' .
        '<core:SET Var="inner"/>'.
        '-{$Var}:{$^Var}:{$#Var}' .
        '</core:datasource>' .
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/set_nested_dataspace.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/set_nested_dataspace.html');
    $output = $page->capture();
    $this->assertEqual($output, 'outer:outer-middle:outer:outer-inner:middle:outer');
  }

  function testFromAttributeDataTakenFromParent()
  {
    $template = '<core:datasource from="{$^middle}">' .
                '{$Var}:{$^Var}:{$#Var}' .
                '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/from_attribute.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/from_attribute.html');
    $page->set('Var', 'outer');
    $page->set('middle', array('Var' => 'middle'));

    $output = $page->capture();
    $this->assertEqual($output, 'middle:outer:outer');
  }

  function testFromAttributeDataTakenFromRoot()
  {
    $template =
        '<core:datasource>' .
        '<core:datasource from="{$#middle}">' .
        '{$Var}' .
        '</core:datasource>'.
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/from_attribute_root.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/from_attribute_root.html');
    $page->set('middle', array('Var' => 'middle'));

    $output = $page->capture();
    $this->assertEqual($output, 'middle');
  }


  function testFromAttributeDataTakenFromPhpVariable()
  {
    $template =
        '<?php $middle = array("Var" => "middle"); ?>'.
        '<core:datasource>' .
        '<core:datasource from="{$$middle}">' .
        '{$Var}' .
        '</core:datasource>'.
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/from_attribute_php_variable.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/from_attribute_php_variable.html');

    $output = $page->capture();
    $this->assertEqual($output, 'middle');
  }


  function testOldFormOfFromAttribute()
  {
    $template =
        '<core:datasource from="middle">' .
        '{$Var}:{$^Var}:{$#Var}' .
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/dataspace/old_form_of_from_attribute.html', $template);
    $page = $this->initTemplate('/tags/core/dataspace/old_form_of_from_attribute.html');
    $page->set('Var', 'outer');
    $page->set('middle', array('Var' => 'middle'));

    $output = $page->capture();
    $this->assertEqual($output, 'middle:outer:outer');
  }
}

