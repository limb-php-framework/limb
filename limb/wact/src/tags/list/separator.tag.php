<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Compile time component for separators in a list
 * The tag work depends on it's position.
 * You MUST place separator at the END of the list item tag content.
 * Default step attribute is 1
 * @tag list:SEPARATOR
 * @restrict_self_nesting
 * @parent_tag_class WactListItemTag
 * @package wact
 * @version $Id: separator.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactListSeparatorTag extends WactRuntimeComponentTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/list/WactListSeparatorComponent.class.php';
  protected $runtimeComponentName = 'WactListSeparatorComponent';

  function preParse($compiler)
  {
    if ($this->getBoolAttribute('literal'))
      return WACT_PARSER_FORBID_PARSING;
  }

  function generateTagContent($code)
  {
    $step_var = $code->getTempVarRef();
    $code->writePHP($step_var . ' = ');

    $this->generateStepAttributeValue($code);
    $code->writePhp(";\n");

    $code->writePhp($this->getComponentRefCode($code) . '->setStep(' . $step_var . ");\n");

    $ListList = $this->findParentByClass('WactListListTag');

    $code->writePhp($this->getComponentRefCode($code) . '->next();' . "\n");

    $code->writePhp('if ( ' . $this->getComponentRefCode($code)  . '->shouldDisplay()){'. "\n");

    $code->writePhp($this->getComponentRefCode($code) . '->reset();'. "\n");

    $separators = $this->parent->findChildrenByClass('WactListSeparatorTag');
    if(array($separators) && count($separators))
    {
      foreach($separators as $separator)
      {
        $code->writePhp('if (');
        $separator->generateStepAttributeValue($code);
        $code->writePhp(' < ' . $step_var . ') ');
        $code->writePhp($separator->getComponentRefCode($code) . "->skipNext();\n");
      }
    }

    parent :: generateTagContent($code);

    $code->writePhp('}'. "\n");
  }

  function generateStepAttributeValue($code)
  {
    if($this->hasAttribute('every'))
      $this->attributeNodes['every']->generateExpression($code);
    elseif($this->hasAttribute('step'))
      $this->attributeNodes['step']->generateExpression($code);
    else
      $code->writePhp("1");
  }
}

