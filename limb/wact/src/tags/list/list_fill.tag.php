<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for output finalizers in a list
 * Allows to generate valid layout while output multicolumb lists
 * Default ratio attribute is 1
 *
 * @tag list:FILL
 * @restrict_self_nesting
 * @req_const_attributes upto
 * @parent_tag_class WactListListTag
 * @package wact
 * @version $Id$
 */
class WactListFillTag extends WactCompilerTag
{
  protected $ratio;
  protected $var_name;

  function preParse($compiler)
  {
    if (!$ratio = $this->getAttribute('upto'))
      $this->ratio = 1;
    else
      $this->ratio = $ratio;

    if($var_name = $this->getAttribute('var'))
      $this->var_name = $var_name;
    else
      $this->var_name = 'items_left';

    if ($this->getBoolAttribute('literal'))
      return WACT_PARSER_FORBID_PARSING;
  }

  function generateTagContent($code)
  {
    $ListList = $this->findParentByClass('WactListListTag');

    $code->writePhp('if (!' . $ListList->getComponentRefCode($code) . '->valid()){' . "\n");

    $count_var = $code->getTempVarRef();
    $items_left_var = $code->getTempVarRef();
    $code->writePhp($count_var .' = '. $ListList->getComponentRefCode($code) . '->countPaginated();');

    $code->writePhp("if ({$count_var}/{$this->ratio} > 1) \n");
    $code->writePhp($items_left_var . " = ceil({$count_var}/{$this->ratio})*{$this->ratio} - {$count_var}; \n");
    $code->writePhp("else \n");
    $code->writePhp($items_left_var . " = 0;\n");

    $code->writePhp("if ({$items_left_var}){\n");

    $code->writePhp($this->getDataSource()->getComponentRefCode() . "['" . $this->var_name . "'] = {$items_left_var};");

    parent :: generateTagContent($code);

    $code->writePhp('}'. "\n");
    $code->writePhp('}'. "\n");
  }
}

