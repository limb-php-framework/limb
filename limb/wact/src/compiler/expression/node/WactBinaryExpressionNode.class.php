<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactBinaryExpressionNode.
 *
 * @package wact
 * @version $Id$
 */
class WactBinaryExpressionNode extends WactTemplateExpressionNode
{
  protected $first_operand;
  protected $second_operand;
  protected $operator;

  function __construct($first, $second, $operator)
  {
    $this->first_operand = $first;
    $this->second_operand = $second;
    $this->operator = $operator;
  }

  function generatePreStatement($code)
  {
    $this->first_operand->generatePreStatement($code);
    $this->second_operand->generatePreStatement($code);
  }

  function generateExpression($code)
  {
    $this->first_operand->generateExpression($code);
    $code->writePHP($this->operator);
    $this->second_operand->generateExpression($code);
  }

  function generatePostStatement($code)
  {
    $this->first_operand->generatePostStatement($code);
    $this->second_operand->generatePostStatement($code);
  }

  function prepare()
  {
    $this->first_operand->prepare();
    $this->second_operand->prepare();
  }
}


