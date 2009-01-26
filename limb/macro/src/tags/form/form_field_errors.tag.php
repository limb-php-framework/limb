<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag form:field_errors
 * @forbid_end_tag   
 * @parent_tag_class lmbMacroFormTag
 * @restrict_self_nesting
 * @package macro
 * @version $Id$
 */
class lmbMacroFormFieldErrorsTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $form = $this->findParentByClass('lmbMacroFormTag')->getRuntimeVar();
    
    $to = $this->get('to');
    
    if($this->has('for'))
      $for = $this->getEscaped('for');
    else
      $for = "";
    
    $code->writePhp("{$to} = {$form}->getErrorsListForFields({$for});\n");

    parent :: _generateContent($code);
  }
}


