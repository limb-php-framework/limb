<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/macro/lmbMacroTemplate.class.php');
lmb_require('limb/view/src/lmbView.class.php');

@define('LIMB_TEMPLATES_INCLUDE_PATH', 'template;limb/*/template');
@define('LIMB_MACRO_TAGS_INCLUDE_PATH', 'src/macro;limb/*/src/macro;limb/macro/src/tags');

/**
 * class lmbMacroView.
 *
 * @package view
 * @version $Id$
 */
class lmbMacroView extends lmbView
{
  protected $macro_template;
  protected $cache_dir;

  function setCacheDir($dir)
  {
    $this->cache_dir = $dir;
  }

  function render()
  {
    if($tpl = $this->_getMacroTemplate())
    {
      $this->_fillMacroTemplate($tpl);
      return $tpl->render();
    }
  }

  function reset()
  {
    parent :: reset();
    $this->macro_template = null;
  }

  function getMacroTemplate()
  {
    return $this->_getMacroTemplate();
  }

  protected function _getMacroTemplate()
  {
    if($this->macro_template)
      return $this->macro_template;

    if(!$path = $this->getTemplate())
      return null;

    $this->macro_template = new lmbMacroTemplate($path, $this->_getMacroConfig()); 
    return $this->macro_template;
  }

  protected function _getMacroConfig()
  {
    return new lmbMacroConfig($this->cache_dir, 
                              true,
                              true, 
                              explode(';', LIMB_TEMPLATES_INCLUDE_PATH),
                              explode(';', LIMB_MACRO_TAGS_INCLUDE_PATH));
  }

  protected function _fillMacroTemplate($template)
  {
    foreach($this->getVariables() as $variable_name => $value)
      $template->set($variable_name, $value);
  }
}

