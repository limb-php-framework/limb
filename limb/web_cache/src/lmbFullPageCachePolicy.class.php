<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCachePolicy.class.php 5013 2007-02-08 15:38:13Z pachanga $
 * @package    web_cache
 */

class lmbFullPageCachePolicy
{
  protected $rulesets;

  function __construct()
  {
    $this->reset();
  }

  function reset()
  {
    $this->rulesets = array();
  }

  function readRules($reader)
  {
    $this->reset();

    foreach($reader->getRulesets() as $rule)
      $this->addRuleset($rule);
  }

  function addRuleset($rule)
  {
    $this->rulesets[] = $rule;
  }

  function findRuleset($request)
  {
    foreach($this->rulesets as $rule)
    {
      if($rule->isSatisfiedBy($request))
        return $rule;
    }
  }

  function getRules()
  {
    return $this->rulesets;
  }
}

?>
