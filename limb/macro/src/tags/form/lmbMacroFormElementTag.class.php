<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroRuntimeWidgetTag.class.php');

/**
 * Base class for any form element tag
 */
class lmbMacroFormElementTag extends lmbMacroRuntimeWidgetTag
{
  function _generateWidget($code_writer)
  {
    parent :: _generateWidget($code_writer);
    if($form_tag = $this->findParentByClass('lmbMacroFormTag'))
    {
      $code_writer->writeToInit("{$this->getRuntimeVar()}->setForm({$form_tag->getRuntimeVar()});\n");
      $code_writer->writeToInit("{$form_tag->getRuntimeVar()}->addChild({$this->getRuntimeVar()});\n");
    }
  }  
}

