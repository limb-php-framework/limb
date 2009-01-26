<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTestCase extends UnitTestCase
{
  protected $toolkit;
  
  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();

    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }
  
  function tearDown()
  {
    lmbToolkit :: restore();
  }

  protected function _createMacro($file)
  {
    $base_dir = LIMB_VAR_DIR . '/tpl';
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file, $this->toolkit->getMacroConfig());
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }

  protected function _createMacroTemplate($code, $name)
  {
    $file = $this->_createTemplate($code, $name);
    return $this->_createMacro($file);
  }
}

