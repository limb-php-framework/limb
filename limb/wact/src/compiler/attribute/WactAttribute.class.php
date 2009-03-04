<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Used to store complex expressions like "{$var1}_my_{$var2}" found inside tag attributes
 * @package wact
 * @version $Id: WactAttribute.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactAttribute implements WactExpressionInterface
{
  protected $name;
  protected $fragments = array();

  function __construct($name, $value = null)
  {
    $this->name = $name;

    if($value)
      $this->addFragment(new WactAttributeLiteralFragment($value));
  }

  function getName()
  {
    return $this->name;
  }

  function addFragment($fragment)
  {
    $this->fragments[] = $fragment;
  }

  function getFragment($index)
  {
    if(isset($this->fragments[$index]))
      return $this->fragments[$index];
  }

  function isConstant()
  {
    $isConstant = TRUE;
    foreach( array_keys($this->fragments) as $key)
      $isConstant = $isConstant && $this->fragments[$key]->isConstant();
    return $isConstant;
  }

  function getValue()
  {
    if(!count($this->fragments))
      return null;

    $value = "";
    foreach( array_keys($this->fragments) as $key)
      $value .= $this->fragments[$key]->getValue();

    return $value;
  }

  function generate($code_writer)
  {
    $code_writer->writeHTML(' ' . $this->name);

    if(!count($this->fragments))
      return;

    $code_writer->writeHTML('="');

    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->generateFragment($code_writer);

    $code_writer->writeHTML('"');
  }

  function generatePreStatement($code_writer)
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('(');
    $separator = '';
    foreach( array_keys($this->fragments) as $key)
    {
      $code_writer->writePHP($separator);
      $this->fragments[$key]->generateExpression($code_writer);
      $separator = ".";
    }
    $code_writer->writePHP(')');
  }

  function generatePostStatement($code_writer)
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->generatePostStatement($code_writer);
  }

  function prepare()
  {
    foreach( array_keys($this->fragments) as $key)
      $this->fragments[$key]->prepare();
  }
}


