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


require_once('limb/wact/src/compiler/attribute/WactAttributeNode.class.php');

/**
* Used to store expressions like "{$var}" found inside tag attributes
*/
class WactAttributeExpression implements WactExpressionInterface
{
  protected $name;
  protected $expression;

  function __construct($name, $expression, $context, $filter_dictionary)
  {
    $this->name = $name;
    $this->expression = new WactExpression($expression, $context, $filter_dictionary, 'raw');
  }

  function getName()
  {
    return $this->name;
  }

  function isConstant()
  {
    return $this->expression->isConstant();
  }

  function getValue()
  {
    return $this->expression->getValue();
  }

  /**
  * Generate the attribute value portion of this attribute
  */
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
      $this->expression->generatePreStatement($code_writer);
      $code_writer->writePHP('echo htmlspecialchars(');
      $this->expression->generateExpression($code_writer);
      $code_writer->writePHP(', ENT_QUOTES);');
      $this->expression->generatePostStatement($code_writer);
    }
  }

  /**
  * Generate the code to output this attribute as part of a tag.
  */
  function generate($code_writer)
  {
    $code_writer->writeHTML(' ' . $this->name);
    $code_writer->writeHTML('="');
    $this->generateFragment($code_writer);
    $code_writer->writeHTML('"');
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