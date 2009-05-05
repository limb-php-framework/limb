<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/wact/lmbWactTemplate.class.php');
lmb_require('limb/view/src/lmbView.class.php');

/**
 * class lmbWactView.
 *
 * @package view
 * @version $Id$
 */
class lmbWactView extends lmbView
{
  protected $wact_template;
  protected $cache_dir;

  function __construct($template_name = '')
  {
    parent :: __construct($template_name);
    $this->cache_dir = LIMB_VAR_DIR . '/compiled/';
  }

  static function locateTemplateByAlias($alias)
  {
    $locator = lmbToolkit :: instance()->getWactLocator();

    if($template_path = $locator->locateSourceTemplate($alias))
      return $template_path;
  }

  function setCacheDir($dir)
  {
    $this->cache_dir = $dir;
  }

  function render()
  {
    if($tpl = $this->_getWactTemplate())
    {
      $this->_fillWactTemplate($tpl);
      return $tpl->capture();
    }
  }

  function reset()
  {
    parent :: reset();
    $this->wact_template = null;
  }

  function getWactTemplate()
  {
    return $this->_getWactTemplate();
  }

  function findChild($id)
  {
    if($tpl = $this->_getWactTemplate())
      return $tpl->findChild($id);
  }

  protected function _getWactTemplate()
  {
    if($this->wact_template)
      return $this->wact_template;

    if(!$path = $this->getTemplate())
      return null;

    $this->wact_template = new lmbWactTemplate($path, $this->cache_dir);
    return $this->wact_template;
  }

  protected function _fillWactTemplate($template)
  {
    foreach($this->getVariables() as $variable_name => $value)
      $template->set($variable_name, $value);

    foreach($this->forms_datasources as $form_id => $datasource)
    {
      $form_component = $template->getChild($form_id);
      $form_component->registerDataSource($datasource);
    }

    foreach($this->forms_errors as $form_id => $error_list)
    {
      $form_component = $template->getChild($form_id);
      if(!$error_list->isValid())
      {
        lmb_require('limb/wact/src/components/form/error.inc.php');
        $wact_error_list = new WactFormErrorList($error_list->getArray());
        $wact_error_list->bindToForm($form_component);
      }
    }
  }
}

