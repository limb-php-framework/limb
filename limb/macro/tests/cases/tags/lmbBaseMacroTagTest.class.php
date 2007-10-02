<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

class lmbBaseMacroTagTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  protected function _createMacro($file)
  {
    $base_dir = LIMB_VAR_DIR . '/tpl';
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file, new lmbMacroConfig($cache_dir, true, true, array($base_dir)));
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}

