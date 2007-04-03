<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheRuleset.class.php 5013 2007-02-08 15:38:13Z pachanga $
 * @package    web_cache
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
