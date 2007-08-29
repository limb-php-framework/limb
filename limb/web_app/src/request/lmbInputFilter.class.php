<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbInputFilter.
 *
 * @package web_app
 * @version $Id: lmbInputFilter.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbInputFilter
{
  protected $rules = array();

  function addRule($rule)
  {
    $this->rules[] = $rule;
  }

  function filter($input)
  {
    $result = $input;

    foreach(array_keys($this->rules) as $key)
      $result = $this->rules[$key]->apply($result);

    return $result;
  }
}

