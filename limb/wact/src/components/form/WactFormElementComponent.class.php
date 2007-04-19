<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

/**
* Base class for concrete form elements
*/
class WactFormElementComponent extends WactRuntimeTagComponent
{
  /**
  * Whether the form element has validated successfully (default TRUE)
  * @var boolean
  */
  protected $IsValid = TRUE;

  /**
  * Human reable name of the form element determined by
  * tag displayname attribute
  * @var string
  */
  public $displayname;

  /**
  * CSS class attribute the element should display if there is an error
  * Determined by tag errorclass attribute
  * @var string
  */
  public $errorclass;

  /**
  * CSS style attribute the element should display if there is an error
  * Determined by tag errorstyle attribute
  * @var string
  */
  public $errorstyle;

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
  */
  function hasErrors()
  {
    return !$this->IsValid;
  }

  /**
  * Puts the element into the error state and assigns the error class or
  * style attributes, if the corresponding member vars have a value
  * (typically you shouldn't need to call this)
  * @return void
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
}
?>
