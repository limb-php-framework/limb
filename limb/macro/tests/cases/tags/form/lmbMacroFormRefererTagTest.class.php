<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFormRefererTagTest extends lmbBaseMacroTest
{
  protected $prev_ref;

  function setUp()
  {
    parent :: setUp();
    $this->prev_ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
  }

  function tearDown()
  {
    parent :: tearDown();
    $_SERVER["HTTP_REFERER"] = $this->prev_ref;
  }

  function testNoReferer()
  {
    $_SERVER["HTTP_REFERER"] = "";

    $template = '{{form name="my_form"}}{{form:referer}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form"></form>');
  }   

  function testReferer()
  {
    $_SERVER["HTTP_REFERER"] = "back.html";

    $template = '{{form name="my_form"}}{{form:referer}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
 
    $out = $page->render();
    $this->assertEqual($out, "<form name=\"my_form\"><input type='hidden' name='referer' value='back.html'></form>");
  }
}
