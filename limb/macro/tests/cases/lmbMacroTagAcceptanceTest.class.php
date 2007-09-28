<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
 
class MacroTagFooTest extends lmbMacroTag
{
  function generateContents($code)
  {
    $code->writeHtml('foo!');
  }
}

class MacroTagBarTest extends lmbMacroTag
{
  function generateContents($code)
  {
    $code->writeHtml('bar');
  }
}

$foo_info = new lmbMacroTagInfo('foo', 'MacroTagFooTest'); 
$bar_info = new lmbMacroTagInfo('bar', 'MacroTagBarTest'); 

lmbMacroTagDictionary :: instance()->register($foo_info, __FILE__);
lmbMacroTagDictionary :: instance()->register($bar_info, __FILE__);

class lmbMacroTagAcceptanceTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testTemplateRendering()
  {
    $code = '<h1>{{foo/}}{{bar/}}</h1>';
    $tpl = $this->_createTemplate($code);
    $out = $tpl->render();
    $this->assertEqual($out, '<h1>foo!bar</h1>');
  }

  protected function _createTemplate($code)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . mt_rand() . '.html';
    file_put_contents($file, $code);
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $tpl = new lmbMacroTemplate($file, $cache_dir);
    return $tpl;
  }
}

