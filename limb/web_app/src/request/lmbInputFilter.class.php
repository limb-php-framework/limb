<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInputFilter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
?>