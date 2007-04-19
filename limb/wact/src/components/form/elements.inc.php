<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: elements.inc.php 5684 2007-04-19 08:34:48Z serega $
 * @package    wact
 */


/**
* Base class for concrete form elements
*/
class WactFormElement extends WactRuntimeTagComponent
{
  /**
  * Whether the form element has validated successfully (default TRUE)
  * @var boolean
  * @access private
  */
  protected $IsValid = TRUE;

  /**
  * Human reable name of the form element determined by
  * tag displayname attribute
  * @var string
  * @access protected
  */
  var $displayname;

  /**
  * CSS class attribute the element should display if there is an error
  * Determined by tag errorclass attribute
  * @var string
  * @access private
  */
  var $errorclass;

  /**
  * CSS style attribute the element should display if there is an error
  * Determined by tag errorstyle attribute
  * @var string
  * @access private
  */
  var $errorstyle;

  /**
  * Returns a value for the name attribute. If $this->displayname is not
  * set, returns either the title, alt or name attribute (in that order
  * of preference, defined for the tag
  * (typically this is called for you by controllers)
  */
  function getDisplayName()
  {
    if (isset($this->displayname))
      return $this->displayname;
    elseif ($this->hasAttribute('title'))
      return $this->getAttribute('title');
    elseif ($this->hasAttribute('alt'))
      return $this->getAttribute('alt');
    else
      return str_replace("_", " ", $this->getAttribute('name'));
  }

  function getName()
  {
    if($this->hasAttribute('name'))
      return $this->getAttribute('name');

    return $this->getId();
  }

  /**
  * Returns true if the form element is in an error state
  * (typically this is called for you by controllers)
  * @return boolean
  * @access protected
  */
  function hasErrors()
  {
      return !$this->IsValid;
  }

  /**
  * Puts the element into the error state and assigns the error class or
  * style attributes, if the corresponding member vars have a value
  * (typically you shouldn't need to call this)
  */
  function setError()
  {
    $this->IsValid = FALSE;
    if (isset($this->errorclass)) {
        $this->setAttribute('class', $this->errorclass);
    }
    if (isset($this->errorstyle)) {
        $this->setAttribute('style', $this->errorstyle);
    }
  }

  /**
  * Returns the value of the form element  (it's value in the form DataSource)
  */
  function getValue()
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    return $form_component->getValue($this->getName());
  }

  /**
  * Sets the value of the form element  (it's value in the form DataSource)
  */
  function setValue($value)
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    return $form_component->setValue($this->getName(), $value);
  }

  /**
  * Overrides WactRuntimeTagComponent method so that requests for the value of
  * the attribute named "value" return the value from the WactFormComponent
  * DataSource, if it exists. This implementation is overridden itself
  * in WactCheckableFormElement
  */
  function getAttribute($name)
  {
    if (strcasecmp($name,'value') == 0)
    {
      if (!is_null($value = $this->getValue()))
        return $value;
    }
    return parent::getAttribute($name);
  }

  /**
  * Overrides WactRuntimeTagComponent method so keep value attribute and value
  * in form DataSource in sync
  */
  function setAttribute($name,$value)
  {
    if (strcasecmp($name,'value') == 0)
      $this->setValue($value);

    parent::setAttribute($name,$value);
  }
}

//--------------------------------------------------------------------------------
/**
* Inherited by InputTextComponent to make sure they
* have a value attribute
*/
class WactInputFormElement extends WactFormElement
{
  /**
  * Overrides then calls with the parent renderAttributes() method. Makes
  * sure there is always a value attribute, even if it's empty.
  * Called from within a compiled template render function.
  * @todo XHTML: Null attributes need a value
  */
  function renderAttributes()
  {
    $value = $this->getValue();
    if (!is_null($value))
      $this->setAttribute('value', $value);
    else
      $this->setAttribute('value', '');

   parent::renderAttributes();
  }
}

//--------------------------------------------------------------------------------
/**
* Represents an HTML label tag
*/
class WactLabelComponent extends WactRuntimeTagComponent
{
  /**
  * CSS class attribute to display on error
  * Determined by tag errorclass attribute
  * @var string
  * @access private
  */
  var $errorclass;

  /**
  * CSS style attribute to display on error
  * Determined by tag errorstyle attribute
  */
  var $errorstyle;

  /**
  * If either are set, assigns the attributes for error class or style
  * @see WactFormComponent::setErrors
  */
  function setError()
  {
    if (isset($this->errorclass))
      $this->setAttribute('class', $this->errorclass);
    if (isset($this->errorstyle))
      $this->setAttribute('style', $this->errorstyle);
  }
}

//--------------------------------------------------------------------------------
/**
* Represents an HTML input type="radio" tag
* Represents an HTML input type="checkbox" tag
*/
class WactCheckableFormElement extends WactFormElement
{
  function getAttribute($name)
  {
    // Can't think of a smarter way do this. Would be nice if WactRuntimeTagComponent
    // wasnt hard coded but rather we skip FormElement
    return WactRuntimeTagComponent::getAttribute($name);
  }

  function setAttribute($name,$value)
  {
    WactRuntimeTagComponent::setAttribute($name,$value);
  }

  function getRawName()
  {
    $name = $this->getAttribute('name');
    return str_replace('[]', '', $name) ;
  }

  function getValue()
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    return $form_component->getValue($this->getRawName());
  }

  /**
  * Overrides then calls with the parent renderAttributes() method dealing
  * with the special case of the checked attribute
  * Called from compiled template
  */
  function renderAttributes()
  {
    if($this->_isChecked())
      $this->setAttribute('checked', "checked");
    else
      $this->removeAttribute('checked');

    parent::renderAttributes();
  }

  protected function _isChecked()
  {
    $value = $this->getValue();

    // We try here really hard to guess if it's checked or not...
    if(is_array($value) && in_array($this->getAttribute('value'), $value))
      return true;
    elseif(is_scalar($value) && $value && $value == $this->getAttribute('value'))
      return true;
    elseif($value && !$this->getAttribute('value'))
      return true;
    elseif($this->getBoolAttribute('checked') && is_null($value))
      return true;
    elseif($value && $value != $this->getAttribute('value'))
      return false;

    return false;
  }
}

?>