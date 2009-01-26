<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroContentBlockAnalizerListener.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroContentBlockAnalizerListener implements lmbMacroBlockAnalizerListener
{
  protected $tree_builder;
  protected $location;

  function __construct($tree_builder, $location)
  {
    $this->tree_builder = $tree_builder;
    $this->location = $location;
  }

  function addLiteralFragment($text)
  {
    $this->tree_builder->addTextNode($text);
  }

  function addExpressionFragment($text)
  {
    $output_expression = new lmbMacroOutputExpressionNode($this->location);

    $expression = new lmbMacroExpressionNode($text,
                                             $output_expression,
                                             $this->tree_builder->getFilterDictionary());
    $output_expression->setExpression($expression);
    
    $this->tree_builder->addNode($output_expression);
  }
}

