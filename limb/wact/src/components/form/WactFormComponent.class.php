<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactFormComponent.
 *
 * @package wact
 * @version $Id: WactFormComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactFormComponent extends WactRuntimeTagComponent
{
  protected $error_list;

  protected $is_valid = TRUE;

  protected $state_vars = array();

  public $datasource;

  function __construct($id)
  {
    parent :: __construct($id);

    $this->datasource = array();
  }

  function get($name)
  {
    return WactTemplate :: getValue($this->datasource, $name);
  }

  /**
  * Set a named property in the form DataSource
  */
  function set($name, $value)
  {
    WactTemplate :: setValue($this->datasource, $name, $value);
  }

  /**
  * Get the named property from the form DataSource
  * @param string variable name
  * @return mixed value or void if not found
  * @access public
  * @deprecated will probablybe removed in a future reorganization of
  *   how form elements become associated with their values
  */
  function getValue($name)
  {
    return WactTemplate :: getValue($this->datasource, $name);
  }

  /**
  * Set a named property in the form DataSource
  */
  function setValue($name, $value)
  {
    WactTemplate :: setValue($this->datasource, $name, $value);
  }

  function registerDataSource($datasource)
  {
    $this->datasource = $datasource;
  }

  function getDataSource()
  {
    return $this->datasource;
  }

  /**
  * Finds the WactLabelComponent associated with a form field, allowing
  * an error message to be displayed next to the field. Called by this
  * setErrors.
  */
  function findLabel($field_id, $component)
  {
    foreach( array_keys($component->children) as $key)
    {
      $child = $component->children[$key];
      if (is_a($child, 'WactLabelComponent') && $child->getAttribute('for') == $field_id)
        return $child;
      elseif ($result = $this->findLabel($field_id, $child))
       return $result;
    }
  }

  /**
  * If errors occur, use this method to identify them to the FormComponent.
  * (typically this is called for you by controllers)
  * @param ErrorList
  */
  function setErrors($ErrorList)
  {
    foreach($ErrorList as $Error)
    {
      $this->is_valid = FALSE;

      // Find the component(s) that the error applies to and tell
      // them there was an error (using their setError() method)
      // as well as notifying related label components if found
      $error_fields = $Error['fields'];
      foreach ($error_fields as $fieldName)
      {
        $Field = $this->findChild($fieldName);
        if (is_object($Field))
        {
          $Field->setError();
          if ($Field->hasAttribute('id'))
          {
            $Label = $this->findLabel($Field->getAttribute('id'), $this);
            if ($Label)
              $Label->setError();
          }
        }
        }
    }

    $this->error_list = $ErrorList;
  }

  function hasErrors()
  {
    return !$this->is_valid;
  }

  /**
  * Returns the ErrorList if it exists or an EmptyErrorList if not
  * (typically this is called for you by controllers)
  * @return object WactErrorList or EmptyArrayIterator
  */
  function getErrorsDataSet()
  {
    if (!isset($this->error_list))
      return new ArrayIterator(array());

    return $this->error_list;
  }

  function getFieldErrorsDataset($for = '')
  {
    if (!isset($this->error_list))
      return new ArrayIterator(array());

    $result = array();
    foreach($this->error_list as $error)
    {
      if(!$for)
        $this->_appendErrorsForEachField($result, $error);
      else
        $this->_appendErrorForField($result, $error, $for);
    }

    return new ArrayIterator($result);
  }

  protected function _appendErrorsForEachField(&$result, $error)
  {
    foreach($error['fields'] as $alias => $field)
    {
      $field_error = $error;
      $field_error['id'] = $field;
      $result[] = $field_error;
    }
  }

  protected function _appendErrorForField(&$result, $error, $field)
  {
    if(!in_array($field, $error['fields']))
      return;

    $field_error = $error;
    $field_error['id'] = $field;
    $result[] = $field_error;
  }

  /**
  * Identify a property stored in the DataSource of the component, which
  * should be passed as a hidden input field in the form post. The name
  * attribute of the hidden input field will be the name of the property.
  * Use this to have properties persist between form submits
  * @see renderState()
  */
  function preserveState($variable)
  {
    $this->state_vars[] = $variable;
  }

  /**
  * Renders the hidden fields for variables which should be preserved.
  * Called from within a compiled template render function.
  */
  function renderState()
  {
    foreach ($this->state_vars as $var)
    {
      echo '<input type="hidden" name="';
      echo $var;
      echo '" value="';
      echo htmlspecialchars($this->getValue($var), ENT_QUOTES);
      echo '"/>';
    }
  }
}

