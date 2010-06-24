<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroRecognizeUrlsFilterTest extends lmbBaseMacroTest
{
  function testSimple()
  {
    $code = '{$#str|recognize_urls}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('str', 'foo http://aaa.com bar');
    $out = $tpl->render();
    $this->assertEqual($out, 'foo <a href="http://aaa.com">http://aaa.com</a> bar');
  }

  function testUrlWithWithoutHttpAndWithWWW()
  {
    $code = '{$#str|recognize_urls}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('str', 'foo www.aaa.com bar');
    $out = $tpl->render();
    $this->assertEqual($out, 'foo <a href="http://www.aaa.com">www.aaa.com</a> bar');
  }
}

