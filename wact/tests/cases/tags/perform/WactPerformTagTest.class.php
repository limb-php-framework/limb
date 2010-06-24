<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/components/perform/WactTemplateCommand.class.php');

class TestingTemplateCommand extends WactTemplateCommand
{
  function doPerform($value1, $value2)
  {
    $this->template->set('my_var', $value1); // root component is always a datasource
    $this->context_component->set('my_var', $value2);
  }

  function doSetOtherText($text)
  {
    return $text;
  }
}

class WactPerformTagTest extends WactTemplateTestCase
{
  function testPerform()
  {
    $template = '<core:datasource>'.
                '<perform command="TestingTemplateCommand">'.
                '  <perform:params value1="Value1" value2="Value2" />'.
                '</perform>'.
                '{$my_var} - {$#my_var}</core:datasource>';

    $this->registerTestingTemplate('/tags/perform/simple.html', $template);

    $page = $this->initTemplate('/tags/perform/simple.html');

    $this->assertEqual(trim($page->capture()), 'Value2 - Value1');
  }

  function testPerformWithOutput()
  {
    $template = '<perform command="TestingTemplateCommand" method="set_other_text">'.
                ' <perform:params text="My Text" />'.
                '</perform>';

    $this->registerTestingTemplate('/tags/perform/with_output.html', $template);

    $page = $this->initTemplate('/tags/perform/with_output.html');

    $this->assertEqual(trim($page->capture()), 'My Text');
  }

  function testPerformWithInclude()
  {
    $template = '<core:datasource>'.
                '<perform command="WactSpecialTestingTemplateCommand" include="limb/wact/tests/cases/tags/perform/WactSpecialTestingTemplateCommand.class.php">'.
                '  <perform:params value1="Value1" value2="Value2" />'.
                '</perform>'.
                '{$my_var} - {$#my_var}</core:datasource>';

    $this->registerTestingTemplate('/tags/perform/simple_with_include.html', $template);

    $page = $this->initTemplate('/tags/perform/simple_with_include.html');

    $this->assertEqual(trim($page->capture()), 'Value2 - Value1');
  }

}

