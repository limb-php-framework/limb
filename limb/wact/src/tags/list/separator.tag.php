<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
 * @version $Id: separator.tag.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class WactListSeparatorTag extends WactRuntimeComponentTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/list/WactListSeparatorComponent.class.php';
  protected $runtimeComponentName = 'WactListSeparatorComponent';

  protected $step;

  function preParse($compiler)
  {
    if($step = $step = $this->getAttribute('every'))
      $this->setAttribute('step', $step);

    if ($step = $this->getAttribute('step'))
      $this->step = $step;
    else
      $this->step = 1;

    if ($this->getBoolAttribute('literal'))
      return WACT_PARSER_FORBID_PARSING;
  }

  function generateTagContent($code)
  {
    $code->writePhp($this->getComponentRefCode($code) . '->setStep(' . $this->step .');' . "\n");

    $ListList = $this->findParentByClass('WactListListTag');

    $code->writePhp($this->getComponentRefCode($code) . '->next();' . "\n");

    $code->writePhp('if ( ' . $this->getComponentRefCode($code)  . '->shouldDisplay()){'. "\n");

    $code->writePhp($this->getComponentRefCode($code) . '->reset();'. "\n");

    $separators = $this->parent->findChildrenByClass('WactListSeparatorTag');
    if(array($separators) && count($separators))
    {
      foreach($separators as $separator)
      {
        if($separator->getAttribute('step') < $this->getAttribute('step'))
          $code->writePhp($separator->getComponentRefCode($code) . "->skipNext();\n");
      }
    }

    parent :: generateTagContent($code);

    $code->writePhp('}'. "\n");
  }
}
?>