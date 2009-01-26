<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTagAttributeBlockAnalizerListener.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagAttributeBlockAnalizerListener implements lmbMacroBlockAnalizerListener
{
  protected $attribute;
  protected $tag_node;
  protected $filter_dictionary;

  function __construct($attribute, $tag_node)
  {
    $this->attribute = $attribute;
    $this->tag_node = $tag_node;
  }

  function addLiteralFragment($text)
  {
    if(strpos($text, '$') === 0)
    {
      $expression = new lmbMacroExpression($text);
      $this->attribute->addExpressionFragment($expression);
    }
    else
      $this->attribute->addTextFragment($text);
  }

  function addExpressionFragment($text)
  {
    $expression = new lmbMacroExpression($text);
    $this->attribute->addExpressionFragment($expression);
  }
}

