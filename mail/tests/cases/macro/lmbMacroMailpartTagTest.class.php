<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroMailpartTagTest extends lmbBaseMacroTest
{
  function testSinglePart()
  {
    $list = '{{mailpart name="test"}}ZZZ{{/mailpart}}';

    $list_tpl = $this->_createTemplate($list, 'mailpart.html');
    $macro = $this->_createMacro($list_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<mailpart name="test"><![CDATA[ZZZ]]></mailpart>');
  }
  
  function testManyParts()
  {
    $list = '{{mailpart name="name_a"}}ZZZ{{/mailpart}}_between_{{mailpart name="name_b"}}YYY{{/mailpart}}';

    $list_tpl = $this->_createTemplate($list, 'mailpart.html');
    $macro = $this->_createMacro($list_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<mailpart name="name_a"><![CDATA[ZZZ]]></mailpart>_between_<mailpart name="name_b"><![CDATA[YYY]]></mailpart>');
  }
}