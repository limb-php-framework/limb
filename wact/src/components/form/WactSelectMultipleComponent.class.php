<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/WactFormElementComponent.class.php';
require_once 'limb/wact/src/components/form/WactOptionRenderer.class.php';

/**
 * Represents an HTML select multiple tag where multiple options
 * can be selected
 * @package wact
 * @version $Id$
 */
class WactSelectMultipleComponent extends WactFormElementComponent
{
  /**
  * A associative array of choices to build the option list with
  * @var array
  * @access private
  */
  var $choice_list = array();

  protected $default_selection = array();
  /**
  * The object responsible for rendering the option tags
  * @var object
  * @access private
  */
  var $option_handler;

  /**
  * Override WactFormElementComponent method to deal with name attributes containing
  * PHP array syntax.
  * @return array the contents of the value
  * @access private
  */
  function getValue()
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $name = str_replace('[]', '', $this->getAttribute('name'));
    return $form_component->getValue($name);
  }

  /**
  * Sets the choice list. Passed an associative array, the keys become the
  * contents of the option value attributes and the values in the array
  * become the text contents of the option tag e.g.
  * <code>
  * $choices = array ( 4 => 'red', 5=>'blue', 6=>'green' );
  * </code>
  * ...becomes...
  * <pre>
  * <select multiple>
  *   <option value="4">red</option>
  *   <option value="5">blue</option>
  *   <option value="6">green</option>
  * </select>
  * </pre>
  * @see setSelection()
  * @param array
  * @return void
  * @access public
  */
  function setChoices($choice_list)
  {
    $this->choice_list = $choice_list;
  }

  function addToChoices($key, $value)
  {
    $this->choice_list[$key] = $value;
  }

  function prependToChoices($key, $value)
  {
    $this->choice_list = array($key => $value) + $this->choice_list;
  }

  function addToDefaultSelection($selection)
  {
    $this->default_selection[] = $selection;
  }

  function setSelection($selection)
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $name = str_replace('[]', '', $this->getAttribute('name'));
    $form_component->setValue($name, $selection);
  }

  /**
  * Sets object responsible for rendering the options
  * Supply your own WactOptionRenderer if the default
  * is too simple
  * @see WactOptionRenderer
  * @param object
  * @return void
  * @access public
  */
  function setOptionRenderer($option_handler) {
      $this->option_handler = $option_handler;
  }

  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  * @return void
  * @access public
  */
  function renderContents()
  {
    $values = $this->getValue();
    if(!is_object($values) && !is_array($values))
      $values = $this->default_selection;

    if(empty($this->option_handler))
      $this->option_handler = new WactOptionRenderer();

    if(!$select_field = $this->getAttribute('select_field'))
      $select_field = 'id';

    $selected_items = array();
    foreach ($values as $value)
    {
      if (is_scalar($value))
        $selected_items[] = $value;
      else
        $selected_items[] = $value[$select_field];
    }
    foreach($this->choice_list as $key => $choice)
    {
      $selected = false;
      if (in_array($key, $selected_items))
        $selected = true;
//      foreach($values as $value)
//      {
//        if(is_scalar($value) && $key == $value)
//        {
//          $selected = true;
//          break;
//        }
//        elseif(!is_scalar($value) && $value[$select_field] == $key)
//        {
//          $selected = true;
//          break;
//        }
//      }

      $this->option_handler->renderOption($key, $choice, $selected);
    }
  }
}


