<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroConfig.class.php');
lmb_require('limb/macro/src/lmbMacroFilter.class.php');
lmb_require('limb/macro/src/lmbMacroFilterInfo.class.php');
lmb_require('limb/macro/src/lmbMacroFilterDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

class MacroFilterFooTest extends lmbMacroFilter
{
  function getValue()
  {
    return 'strtoupper(' . $this->base->getValue() . ')';
  }
}

class MacroFilterZooTest extends lmbMacroFilter
{
  function getValue()
  {
    return 'trim(' . $this->base->getValue() . ')';
  }
}

$foo_filter_info = new lmbMacroFilterInfo('uppercase', 'MacroFilterFooTest');
$foo_filter_info->setFile(__FILE__);
$zoo_filter_info = new lmbMacroFilterInfo('trim', 'MacroFilterZooTest');
$zoo_filter_info->setFile(__FILE__);

lmbMacroFilterDictionary :: instance()->register($foo_filter_info);
lmbMacroFilterDictionary :: instance()->register($zoo_filter_info);

class lmbMacroFiltersTest extends lmbBaseMacroTest
{
  function testFilter()
  {
    $code = '{$#var|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'hello');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }

  function testFilterChain()
  {
    $code = '{$#var|trim|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  hello  ');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }
}

