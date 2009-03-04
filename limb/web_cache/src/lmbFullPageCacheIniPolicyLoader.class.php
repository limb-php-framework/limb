<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_cache/src/lmbFullPageCachePolicy.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRuleset.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUriPathRule.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheUserRule.class.php');
lmb_require('limb/web_cache/src/lmbFullPageCacheRequestRule.class.php');
lmb_require('limb/config/toolkit.inc.php');

/**
 * class lmbFullPageCacheIniPolicyLoader.
 *
 * @package web_cache
 * @version $Id: lmbFullPageCacheIniPolicyLoader.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbFullPageCacheIniPolicyLoader
{
  protected $ini;

  function __construct($ini_path)
  {
    $this->ini = lmbToolkit :: instance()->getConf($ini_path);
  }

  function load()
  {
    $policy = new lmbFullPageCachePolicy();
    $groups = $this->ini->getAll();
    ksort($groups);

    foreach($groups as $rule_name => $options)
    {
      $ruleset = new lmbFullPageCacheRuleset();

      if(isset($options['type']) && $options['type'] == 'deny')
        $ruleset->setType(false);

      if(isset($options['path_regex']))
      {
        $rule = new lmbFullPageCacheUriPathRule($options['path_regex']);

        if(isset($options['path_offset']))
          $rule->useOffset(rtrim($options['path_offset'], '/'));
        $ruleset->addRule($rule);
      }

      if(isset($options['groups']))
      {
        $rule = new lmbFullPageCacheUserRule($options['groups']);
        $ruleset->addRule($rule);
      }

      if(isset($options['request']) || isset($options['get']) || isset($options['post']))
      {
        $rule = new lmbFullPageCacheRequestRule(isset($options['request']) ? $options['request'] : null,
                                                isset($options['get']) ? $options['get'] : null,
                                                isset($options['post']) ? $options['post'] : null);
        $ruleset->addRule($rule);
      }

      $policy->addRuleset($ruleset);
    }
    return $policy;
  }
}


