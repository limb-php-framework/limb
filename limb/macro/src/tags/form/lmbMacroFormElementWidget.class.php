<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
lmb_require('limb/macro/src/compiler/lmbMacroHtmlTagWidget.class.php');

/**
 * class lmbMacroFormElementWidget.
 * Base class for any form fields object at runtime 
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFormElementWidget extends lmbMacroHtmlTagWidget
{
  protected $has_errors = false;
  protected $form;
  
  function __construct($id)
  {
    parent :: __construct($id);
    
    $this->skip_render = array_merge($this->skip_render, array('error_class', 'error_style'));
  }
  
  function getDisplayName()
  {
    if($this->hasAttribute('title'))
      return $this->getAttribute('title');
    if($this->hasAttribute('alt'))
      return $this->getAttribute('alt');
    
    return $this->runtime_id;
  }
  
  function setForm($form)
  {
    $this->form = $form;
  }
  
  function setErrorState($has_errors = true)
  {
    $this->has_errors = $has_errors;
    
    if($has_errors)
    {
      if($this->hasAttribute('error_class')) 
        $this->setAttribute('class', $this->getAttribute('error_class'));
      if($this->hasAttribute('error_style')) 
       $this->setAttribute('style', $this->getAttribute('error_style'));
    }
  }
  
  function hasErrors()
  {
    return $this->has_errors;
  }
  
  function getValue()
  {
    if($this->hasAttribute('value'))
      return $this->getAttribute('value');
    
    return $this->_getValueFromFormDatasource();
  }   
  
  function getName()
  {
    if($this->hasAttribute('name'))
      return $this->getAttribute('name');

    return $this->getRuntimeId();
  }   
  
  protected function _getValueFromFormDatasource()
  {
    if(is_object($this->form))
    {
      $ds = $this->form->getDatasource();
      $id = $this->getName();
      if(isset($ds[$id]))
        return $ds[$id];
    }
  }

  function renderAttributes()
  {
    foreach(array('readonly', 'disabled') as $attribute)
    {
      if(!$this->getBoolAttribute($attribute, false))
        $this->removeAttribute($attribute);
    }
    parent :: renderAttributes();
  }
}

