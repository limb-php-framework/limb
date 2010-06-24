<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbFullPageCachePolicy.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCachePolicy.class.php 7686 2009-03-04 19:57:12Z korchasa $
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


