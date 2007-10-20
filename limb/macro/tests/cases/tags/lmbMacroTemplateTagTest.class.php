<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTemplateTagTest extends lmbBaseMacroTest
{
  function testApplyTemplate()
  {
    $content = '{{template name="tpl1"}}' .  
               '{$bar}' .                   
               '{{/template}}' .              
               '{{template name="tpl2"}}' .  
               '{$foo}{$hey}' .                   
               '{{/template}}' .
               '{{apply template="tpl2" hey="$#hey" foo="$#foo"/}}' .
               '{{apply template="tpl1" bar="$#bar"/}}' 
               ;

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('hey', 'HEY');
    $macro->set('bar', 'BAR');
    $macro->set('foo', 'FOO');

    $out = $macro->render();
    $this->assertEqual($out, 'FOOHEYBAR');
  }
}

