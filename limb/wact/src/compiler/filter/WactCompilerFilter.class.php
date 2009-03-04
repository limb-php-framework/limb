<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactCompilerFilter.
 *
 * @package wact
 * @version $Id: WactCompilerFilter.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCompilerFilter implements WactExpressionInterface
{
  protected $base;
  protected $parameters = array();
  protected $location_in_template;

  function __construct($location)
  {
    $this->location_in_template = $location;
  }

  function registerBase($base)
  {
    $this->base = $base;
  }

  function registerParameter($parameter)
  {
    $this->parameters[] = $parameter;
  }

  function isConstant()
  {
    $isConstant = $this->base->isConstant();
    foreach( array_keys($this->parameters) as $key)
      $isConstant = $isConstant && $this->parameters[$key]->isConstant();
    return $isConstant;
  }

  function raiseUnresolvedBindingError()
  {
    throw new WactException('Cannot resolve expression (must resolve to a constant value)');
  }

  function getValue()
  {
  }

  function generatePreStatement($code_writer)
  {
    $this->base->generatePreStatement($code_writer);
    foreach( array_keys($this->parameters) as $key)
      $this->parameters[$key]->generatePreStatement($code_writer);;
  }

  function generateExpression($code_writer)
  {
  }

  function generatePostStatement($code_writer)
  {
    $this->base->generatePostStatement($code_writer);
    foreach( array_keys($this->parameters) as $key)
        $this->parameters[$key]->generatePostStatement($code_writer);;
  }

  function prepare()
  {
    $this->base->prepare();
    foreach( array_keys($this->parameters) as $key)
        $this->parameters[$key]->prepare();
  }
}


