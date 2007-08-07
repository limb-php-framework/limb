<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactFormComponent.
 *
 * @package wact
 * @version $Id: WactFormComponent.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class WactFormComponent extends WactRuntimeTagComponent
{
  protected $error_list;

  protected $is_valid = TRUE;

  protected $state_vars = array();

  protected $_datasource;

  protected function _ensureDataSourceAvailable()
  {
    if (!isset($this->_datasource))
      $this->registerDataSource(new ArrayObject());
  }

  function get($name)
  {
    $this->_ensureDataSourceAvailable();
    return $this->_datasource->get($name);
  }

  function set($name, $value)
  {
    $this->_ensureDataSourceAvailable();
    $this->_datasource->set($name, $value);
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
    $this->_ensureDataSourceAvailable();
    return $this->_datasource->get($name);
  }

  /**
  * Set a named property in the form DataSource
  */
  function setValue($name, $value)
  {
    $this->_ensureDataSourceAvailable();
    $this->_datasource->set($name, $value);
  }

  function prepare()
  {
    $this->_ensureDataSourceAvailable();
  }

  function registerDataSource($datasource)
  {
    $this->_datasource = WactTemplate :: makeObject($datasource);
  }

  function getDataSource()
  {
    return $this->_datasource;
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
      foreach ($Error->getFields() as $fieldName)
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
    $fields = $error->getFields();
    foreach($fields as $alias => $field)
    {
      $field_error = clone($error);
      $field_error['id'] = $field;
      $result[] = $field_error;
    }
  }

  protected function _appendErrorForField(&$result, $error, $field)
  {
    if(!in_array($field, $error->getFields()))
      return;

    $field_error = clone($error);
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

