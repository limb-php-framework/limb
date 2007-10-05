<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactDataBindingExpressionNode.
 *
 * @package wact
 * @version $Id: WactDataBindingExpressionNode.class.php 6386 2007-10-05 14:22:21Z serega $
 */
class WactDataBindingExpressionNode
{
  protected $context;
  protected $datasource_context;
  protected $original_expression;
  protected $processed_expression;

  protected $path_to_target_datasource;
  protected $field_name = null;

  protected $datasource_ref_var;

  protected $expression_analyzed = FALSE;

  protected $is_property = false;
  protected $property;
  protected $php_variable = false;

  function __construct($expression, $context_node, $datasource_context = null)
  {
    $this->original_expression = $expression;
    $this->context = $context_node;

    if($datasource_context)
      $this->datasource_context = $datasource_context;
    else
      $this->datasource_context = $context_node;
  }

  function analyzeExpression()
  {
    if ($this->expression_analyzed)
      return;

    $this->_findRealContext();

    if(!$this->datasource_context && !$this->php_variable)
      $this->context->raiseCompilerError('Expression datasource context not found', array('expression' => $this->original_expression));

    $this->_extractPathToTargetDatasource();

    $this->_extractTargetFieldName();

    if (is_object($this->datasource_context))
    {
      $this->property = $this->datasource_context->getProperty($this->field_name);
      if (is_object($this->property))
        $this->property->activate();
    }

    $this->expression_analyzed = TRUE;
  }

  function getDatasourceContext()
  {
    $this->analyzeExpression();
    return $this->datasource_context;
  }

  protected function _findRealContext()
  {
    $this->processed_expression = $this->original_expression;

    if(strpos($this->processed_expression, ":") !== false)
      $this->is_property = true;

    do
    {
      $modifier = $this->processed_expression{0};

      // the same datasource
      if ($modifier == ".")
      {
        $this->processed_expression = substr($this->processed_expression, 1);
        continue;
      }

      // local PHP variable
      if ($modifier == "$")
      {
        $this->datasource_context = null;
        $this->php_variable = true;
        $this->processed_expression = substr($this->processed_expression, 1);
        return;
      }

      // tag property
      if ($modifier == ":")
      {
        $this->processed_expression = substr($this->processed_expression, 1);
        return;
      }

      // root context
      if ($modifier == "#")
      {
        $this->datasource_context = $this->datasource_context->getRootDataSource();
        $this->processed_expression = substr($this->processed_expression, 1);
        continue;
      }

      // parent context
      if ($modifier == "^")
      {
        if(!$this->is_property)
          $this->datasource_context = $this->datasource_context->getParentDataSource();
        else
          $this->datasource_context = $this->datasource_context->getParent();

        $this->processed_expression = substr($this->processed_expression, 1);
        continue;
      }

      // child context
      if($modifier == '[')
      {
        $pos = strpos($this->processed_expression, ']');
        $context_name = substr($this->processed_expression, 1, $pos - 1);
        $this->datasource_context = $this->datasource_context->findChild($context_name);
        if(!$this->datasource_context)
          $this->context->raiseCompilerError('None existing expression datasource context', array('expression' => $this->original_expression));

        $this->processed_expression = substr($this->processed_expression, $pos+1);
        continue;
      }

      break;
    }
    while(true);
  }

  protected function _extractPathToTargetDatasource()
  {
    $pos = strpos($this->processed_expression, '.');
    if (!is_integer($pos))
      return;

    $this->path_to_target_datasource = array();
    while (preg_match('/^(\w+)\.((?s).*)$/', $this->processed_expression, $match))
    {
      $this->path_to_target_datasource[] = $match[1];
      $this->processed_expression = $match[2];
    }
  }

  protected function _extractTargetFieldName()
  {
    if(is_null($this->processed_expression) || ($this->processed_expression === false))
      return;

    if (preg_match("/^\w+$/", $this->processed_expression))
      $this->field_name = $this->processed_expression;
    else
      $this->context->raiseCompilerError('Invalid data binding', array('expression' => $this->original_expression));
  }

  function prepare()
  {
    $this->analyzeExpression();
  }

  function getFieldName()
  {
    $this->analyzeExpression();
    return $this->field_name;
  }

  function getPathToTargetDatasource()
  {
    $this->analyzeExpression();
    return $this->path_to_target_datasource;
  }

  function isConstant()
  {
    $this->analyzeExpression();

    if($this->php_variable)
      return false;

    if (is_null($this->datasource_context))
      return TRUE;

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

    if($this->php_variable)
    {
      $this->datasource_ref_var = '$' . $key;
    }
    else
    {
      $code_writer->writePHP($this->datasource_ref_var . '= WactTemplate::getValue(' . $this->datasource_context->getDataSource()->getDatasourceRefCode() . ',');
      $code_writer->writePHPLIteral($key);
      $code_writer->writePHP(');');
    }

    foreach ($this->path_to_target_datasource as $key)
    {
      $datasource_ref_var = $code_writer->getTempVarRef();
      $code_writer->writePHP($datasource_ref_var . '= WactTemplate::getValue(' . $this->datasource_ref_var . ',');
      $code_writer->writePHPLIteral($key);
      $code_writer->writePHP(');');
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

    if($this->php_variable)
    {
      if($this->datasource_ref_var)
      {
        $code_writer->writePHP('WactTemplate::getValue(' . $this->datasource_ref_var . ',');
        $code_writer->writePHPLiteral($this->field_name);
        $code_writer->writePHP(')');
        return;
      }
      else
      {
        $code_writer->writePHP('$' . $this->field_name);
        return;
      }
    }

    if (isset($this->datasource_ref_var))
    {
      $code_writer->writePHP('WactTemplate::getValue(' . $this->datasource_ref_var . ',');
      $code_writer->writePHPLiteral($this->field_name);
      $code_writer->writePHP(')');
    }
    else
    {
      if(!is_null($this->field_name))
      {
        $code_writer->writePHP('WactTemplate::getValue(' . $this->datasource_context->getDatasource()->getDatasourceRefCode() . ',');
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


