<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * abstract class lmbView.
 *
 * @package view
 * @version $Id$
 */
abstract class lmbView
{
  protected $template_name;
  protected $variables = array();
  protected $forms_datasources = array();
  protected $forms_errors = array();

  function __construct($template_name)
  {
    $this->template_name = $template_name;
  }

  abstract function render();

  function reset()
  {
    $this->forms_datasources = array();
    $this->forms_errors = array();
    $this->variables = array();
  }

  function copy($view)
  {
    $this->variables = $view->variables;
    $this->forms_errors = $view->forms_errors;
    $this->forms_datasources = $view->forms_datasources;
  }

  function getTemplate()
  {
    return $this->template_name;
  }

  function set($variable_name, $value)
  {
    $this->variables[$variable_name] = $value;
  }

  function setVariables($vars)
  {
    $this->variables = $vars;
  }

  function get($variable_name)
  {
    if(isset($this->variables[$variable_name]))
      return $this->variables[$variable_name];
  }

  function getVariables()
  {
    return $this->variables;
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

}

