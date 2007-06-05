<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/wact/lmbWactTemplate.class.php');
lmb_require('limb/view/src/lmbView.class.php');

class lmbWactView extends lmbView
{
  protected $wact_template;
  protected $forms_datasources = array();
  protected $forms_errors = array();
  protected $cache_dir;

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
    $this->forms_datasources = array();
    $this->forms_errors = array();
    $this->wact_template = null;
  }

  function getWactTemplate()
  {
    return $this->_getWactTemplate();
  }

  function setFormDatasource($form_name, $datasource)
  {
    $this->forms_datasources[$form_name] = $datasource;
  }

  function getFormDatasource($form_name)
  {
    if(isset($this->forms_datasources[$form_name]))
      return $this->forms_datasources[$form_name];
    else
      return null;
  }

  function setFormErrors($form_name, $error_list)
  {
    $this->forms_errors[$form_name] = $error_list;
  }

  function getForms()
  {
    return $this->forms_datasources;
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
        $error_list->setFieldNameDictionary(new WactFormFieldNameDictionary($form_component));
        $form_component->setErrors($error_list->getReadable());
      }
    }
  }
}
?>
