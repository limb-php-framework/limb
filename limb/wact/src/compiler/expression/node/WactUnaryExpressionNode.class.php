<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

class WactUnaryExpressionNode extends WactTemplateExpressionNode
{
  protected $operand;
  protected $operator;

  function __construct($operand, $operator)
  {
    $this->operand = $operand;
    $this->operator = $operator;
  }

  function generatePreStatement($code)
  {
    $this->operand->generatePreStatement($code);
  }

  function generateExpression($code)
  {
    $code->writePHP($this->operator);
    $this->operand->generateExpression($code);
  }

  function generatePostStatement($code)
  {
    $this->operand->generatePostStatement($code);
  }

  function prepare()
  {
    $this->operand->prepare();
  }
}
?>
