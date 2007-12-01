<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
lmb_require('limb/macro/src/tags/form/lmbMacroHtmlTagWidget.class.php');

/**
 * class lmbMacroFormLabelFieldWidget.
 * Runtime class for {{label}} tag that renders html <label> tag   
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFormLabelWidget extends lmbMacroHtmlTagWidget
{
  protected $has_errors = false;
  
  function setErrorState($has_errors = true)
  {
    $this->has_errors = $has_errors;
  }
  
  function hasErrors()
  {
    return $this->has_errors;
  }   
}

