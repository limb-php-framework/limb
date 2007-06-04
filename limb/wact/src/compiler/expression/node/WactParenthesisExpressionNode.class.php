<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

class WactParenthesisExpressionNode extends WactTemplateExpressionNode
{
  protected $operand;

  function __construct($operand)
  {
    $this->operand = $operand;
  }

  function generatePreStatement($code)
  {
    $this->operand->generatePreStatement($code);
  }

  function generateExpression($code)
  {
    $code->writePHP('(');
    $this->operand->generateExpression($code);
    $code->writePHP(')');
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
