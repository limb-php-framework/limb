<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/compiler/lmbMacroFilter.class.php');

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
    if(!isset($this->params[0]))
      return 'trim(' . $this->base->getValue() . ')';
    else
      return 'trim(' . $this->base->getValue() . ', ' . $this->params[0] . ')';
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
  
  function testFilterWithParams()
  {
    $code = '{$#var|trim|trim:"/"|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  /hello/  ');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }

  function testFilterWithVariablesInParams()
  {
    $code = '{$#var|trim|trim:$#foo|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  /hello/  ');
    $tpl->set('foo', '/');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }

  function testFilterWithPHPCodeInParams()
  {
    $code = '{$#var|trim|trim:$#foo . $#bar|uppercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '  #/hello/#  ');
    $tpl->set('foo', '/');
    $tpl->set('bar', '#');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }
  
  function testApplyHtmlFilterByDefault()
  {
    $code = '{$#var}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '<>');
    $out = $tpl->render();
    $this->assertEqual($out, '&lt;&gt;');
  }  

  function testDoesNotApplyHtmlFilterIfOutFilterPresent()
  {
    $code = '{$#var|trim}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '<>');
    $out = $tpl->render();
    $this->assertEqual($out, '<>');
  }    
}

