<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * abstract class lmbOciLob.
 *
 * @package dbal
 * @version $Id: lmbOciLob.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
abstract class lmbOciLob
{
  protected $value;

  function __construct($value)
  {
    $this->value = $value;
  }

  abstract function getNativeType();
  abstract function getEmptyExpression();
  abstract function getDescriptorType();

  function read()
  {
    return $this->value;
  }
}


