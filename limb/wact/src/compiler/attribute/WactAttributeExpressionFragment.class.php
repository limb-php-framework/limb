<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactAttributeExpression.class.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
 */


/**
* Used to store expressions like "{$var}" found inside tag attributes
*/
class WactAttributeExpressionFragment implements WactExpressionInterface
{
  protected $expression;

  function __construct($expression, $context, $filter_dictionary)
  {
    $this->expression = new WactExpression($expression, $context, $filter_dictionary, 'raw');
  }

  function isConstant()
  {
    return $this->expression->isConstant();
  }

  function getValue()
  {
    return $this->expression->getValue();
  }

  function generateFragment($code_writer)
  {
    if ($this->isConstant())
    {
      $value = $this->getValue();
      if (!is_null($value))
        $code_writer->writeHTML(htmlspecialchars($value, ENT_QUOTES));
    }
    else
    {
      $code_writer->writePHP('echo htmlspecialchars(');
      $this->expression->generateExpression($code_writer);
      $code_writer->writePHP(', ENT_QUOTES);');
    }
  }

  function generatePreStatement($code_writer)
  {
    $this->expression->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $this->expression->generateExpression($code_writer);
  }

  function generatePostStatement($code_writer)
  {
    $this->expression->generatePostStatement($code_writer);
  }

  function prepare()
  {
    return $this->expression->prepare();
  }

  function getExpression()
  {
    return $this->expression;
  }
}
?>