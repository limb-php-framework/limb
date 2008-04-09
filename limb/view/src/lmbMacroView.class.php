<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/view/src/lmbView.class.php');

/**
 * class lmbMacroView.
 *
 * @package view
 * @version $Id$
 */
class lmbMacroView extends lmbView
{
  protected $macro_template;

  static function locateTemplateByAlias($alias)
  {
    $locator = lmbToolkit :: instance()->getMacroLocator();

    if($template_path = $locator->locateSourceTemplate($alias))
      return $template_path;
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

    $toolkit = lmbToolkit :: instance();
    $this->macro_template = new lmbMacroTemplate($path, $toolkit->getMacroConfig(), $toolkit->getMacroLocator()); 
    return $this->macro_template;
  }

  protected function _fillMacroTemplate($template)
  {
    foreach($this->getVariables() as $variable_name => $value)
      $template->set($variable_name, $value);
    
    foreach($this->forms_datasources as $form_id => $datasource)
      $template->set('form_' . $form_id . '_datasource', $datasource);

    foreach($this->forms_errors as $form_id => $error_list)
      $template->set('form_' . $form_id . '_error_list', $error_list->getArray());
  }
}

