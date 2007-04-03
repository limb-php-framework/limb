<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWactView.class.php 5241 2007-03-14 13:03:47Z serega $
 * @package    view
 */
lmb_require('limb/view/src/wact/lmbWactTemplate.class.php');
lmb_require('limb/view/src/lmbView.class.php');

class lmbWactView extends lmbView
{
  protected $wact_template;
  protected $forms_datasources = array();
  protected $forms_errors = array();

  function render()
  {
    $this->_initWactTemplate();
    $this->_fillWactTemplate();
    return $this->wact_template->capture();
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
    $this->_initWactTemplate();
    return $this->wact_template;
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
    $this->_initWactTemplate();
    return $this->wact_template->findChild($id);
  }

  protected function _initWactTemplate()
  {
    if($this->wact_template)
      return;

    if(!$path = $this->getTemplate())
      throw new lmbException("Could not init WACT template '{$this->template_name}'");

    $this->wact_template = new lmbWactTemplate($path);
  }

  protected function _fillWactTemplate()
  {
    foreach($this->getVariables() as $variable_name => $value)
      $this->wact_template->set($variable_name, $value);

    foreach($this->forms_datasources as $form_id => $datasource)
    {
      $form_component = $this->wact_template->getChild($form_id);
      $form_component->registerDataSource($datasource);
    }

    foreach($this->forms_errors as $form_id => $error_list)
    {
      $form_component = $this->wact_template->getChild($form_id);
      if(!$error_list->isValid())
        $form_component->setErrors($error_list);
    }
  }
}
?>
