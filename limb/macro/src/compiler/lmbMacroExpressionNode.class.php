<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroExpressionNode.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroExpressionNode  implements lmbMacroExpressionInterface
{
  protected $context;

  protected $original_expression;
  protected $expression;

  protected $parsed;

  protected $filter_dictionary;

  function __construct($expression, $context_node, $filter_dictionary)
  {
    $this->original_expression = $expression;
    $this->expression = $expression;
    $this->context = $context_node;
    $this->filter_dictionary = $filter_dictionary;

    $this->_createParsedExpression();
  }

  protected function _createParsedExpression()
  {
    $pos = strpos($this->expression, "|");

    if ($pos === FALSE)
    {
      $filters_expression = 'html';
      $base_expression = $this->expression;
    }
    else
    {
      $base_expression = trim(substr($this->expression, 0, $pos));
      $filters_expression = trim(substr($this->expression, $pos + 1));
    }
    
    $this->parsed = $this->createFilterChain($filters_expression, new lmbMacroExpression($base_expression));
  }

  /**
  * Parses an expression, building a chain of filters for it
  */
  function createFilterChain($expression, $base)
  {
    $filter_parser = new lmbMacroFilterParser($this->context);
    $filters_specs = $filter_parser->parse($expression);

    foreach($filters_specs as $filter_spec)
    {
      $filter_name = $filter_spec['name'];
      $filter_info = $this->filter_dictionary->findFilterInfo($filter_name);

      if (!is_object($filter_info))
        $this->context->raise('Unknown filter', array('filter' => $filter_name));

      $base = $this->_createFilter($filter_name, $base, $filter_spec['params']);
    }

    return $base;
  }

  protected function _createFilter($name, $base, $params = "")
  {
    $filter_info = $this->filter_dictionary->findFilterInfo($name);

    if (!is_object($filter_info))
      $this->context->raise('Unknown filter', array('filter' => $name));

    $filter_info->load();

    $filter_class = $filter_info->getClass();
    $filter = new $filter_class($base);
    if(sizeof($params))
      $filter->setParams($params);
    return $filter;
  }

  function getValue()
  {
    return $this->parsed->getValue();
  }

  function preGenerate($code_writer)
  {
    $this->parsed->preGenerate($code_writer);
  }

  function getFilterDictionary()
  {
    return $this->filter_dictionary;
  }
}

