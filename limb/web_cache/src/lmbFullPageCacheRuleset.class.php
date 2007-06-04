<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheRuleset.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class lmbFullPageCacheRuleset
{
  protected $rules = array();
  protected $type = true;

  function __construct($type = true)
  {
    $this->type = $type;
  }

  function setType($type)
  {
    return $this->type = $type;
  }

  function isAllow()
  {
    return $this->type == true;
  }

  function isDeny()
  {
    return $this->type == false;
  }

  function addRule($rule)
  {
    $this->rules[] = $rule;
  }

  function isSatisfiedBy($request)
  {
    foreach($this->rules as $rule)
    {
      if(!$rule->isSatisfiedBy($request))
        return false;
    }

    return true;
  }
}

?>
