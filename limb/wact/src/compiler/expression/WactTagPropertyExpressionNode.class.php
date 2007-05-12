<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDataBindingExpressionNode.class.php 5750 2007-04-23 13:56:35Z serega $
 * @package    wact
 */


class WactTagPropertyExpressionNode
{
  protected $context;
  protected $property;

  function __construct($expression, $context)
  {
    $this->original_expression = $expression;
    $this->context = $context_node;
  }

  function analyzeExpression()
  {
    if ($this->expression_analyzed)
      return;

    /* pre-defined properties will never be found inside a child datasource context */
    if (is_object($this->context))
    {
      $this->property = $this->context->getProperty($this->field_name);
      if (is_object($this->property))
        $this->property->activate();
    }

    $this->expression_analyzed = TRUE;
  }

  function prepare()
  {
    $this->analyzeExpression();
  }

  function isConstant()
  {
    $this->analyzeExpression();

    if (is_object($this->property))
      return $this->property->isConstant();

    return FALSE;
  }

  /**
  * Return the value of this expression
  */
  function getValue()
  {
    $this->analyzeExpression();

    if (is_null($this->property) || !$this->property->isConstant())
      $this->datasource_context->raiseCompilerError('Cannot resolve data binding', array('expression' => $this->original_expression));
    else
      return $this->property->getValue();
  }

  /**
  * Generate setup code for an expression reference
  */
  function generatePreStatement($code_writer)
  {
    $this->analyzeExpression();

    if (is_object($this->property))
      $this->property->generatePreStatement($code_writer);

    $this->_generateReferencesChainToTargetDatasource($code_writer);
  }

  protected function _generateReferencesChainToTargetDatasource($code_writer)
  {
    if (!isset($this->path_to_target_datasource))
      return;

    $key = array_shift($this->path_to_target_datasource);

    $this->datasource_ref_var = $code_writer->getTempVarRef();

    $code_writer->writePHP($this->datasource_ref_var . '= WactTemplate :: makeObject(' . $this->datasource_context->getDataSource()->getComponentRefCode() . '->get(');
    $code_writer->writePHPLIteral($key);
    $code_writer->writePHP('));');

    foreach ($this->path_to_target_datasource as $key)
    {
      $datasource_ref_var = $code_writer->getTempVarRef();
      $code_writer->writePHP($datasource_ref_var . '= WactTemplate :: makeObject(' . $this->datasource_ref_var . '->get(');
      $code_writer->writePHPLIteral($key);
      $code_writer->writePHP('));');
      $this->datasource_ref_var = $datasource_ref_var;
    }
  }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  */
  function generateExpression($code_writer)
  {
    $this->analyzeExpression();

    if (is_object($this->property))
    {
      $this->property->generateExpression($code_writer);
      return;
    }

    if (isset($this->datasource_ref_var))
    {
      $code_writer->writePHP($this->datasource_ref_var . '->get(');
      $code_writer->writePHPLiteral($this->field_name);
      $code_writer->writePHP(')');
    }
    else
    {
      if($this->field_name)
      {
        $code_writer->writePHP('' . $this->datasource_context->getDatasource()->getComponentRefCode() . '->get(');
        $code_writer->writePHPLiteral($this->field_name);
        $code_writer->writePHP(')');
      }
      else
      {
        $code_writer->writePHP($this->datasource_context->getComponentRefCode());
      }
    }
  }

  function generatePostStatement($code_writer)
  {
    $this->analyzeExpression();

    if (is_object($this->property))
      $this->property->generatePostStatement($code_writer);
  }
}

?>