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

//--------------------------------------------------------------------------------
/**
 * Represents an HTML select tag where only a single option can
 * be selected
 * @package wact
 * @version $Id$
 */
class WactSelectSingleComponent extends WactFormElementComponent
{
  /**
  * A associative array of choices to build the option list with
  * @var array
  * @access private
  */
  var $choice_list = array();

  protected $default_selection = null;
  /**
  * The object responsible for rendering the option tags
  * @var object
  * @access private
  */
  var $option_handler;

  /**
  * Sets the choice list. Passed an associative array, the keys become the
  * contents of the option value attributes and the values in the array
  * become the text contents of the option tag e.g.
  * <code>
  * $choices = array ( 4 => 'red', 5=>'blue', 6=>'green' );
  * </code>
  * ...becomes...
  * <pre>
  * <select>
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
    $this->default_selection = $selection;
  }

  function setSelection($selection)
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $form_component->setValue($this->getAttribute('name'), $selection);
  }

  /**
  * Sets object responsible for rendering the options
  * Supply your own WactOptionRenderer if the default
  * is too simple
  * @see WactOptionRenderer
  */
  function setOptionRenderer($option_handler)
  {
    $this->option_handler = $option_handler;
  }

  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  */
  function renderContents()
  {
    $value = $this->getValue();
    if(is_null($value))
      $value = $this->default_selection;

    if(!is_object($this->option_handler))
      $this->option_handler = new WactOptionRenderer();

    if(!$select_field = $this->getAttribute('select_field'))
      $select_field = 'id';

    if(!is_scalar($value))
      $selected = $value[$select_field];
    else
      $selected = $value;

    foreach($this->choice_list as $key => $choice)
    {
      //special case, since in PHP "0 == 'bar'"
      $set = ((string)$key) == $selected;
      $this->option_handler->renderOption($key, $choice, $set);
    }
  }
}


