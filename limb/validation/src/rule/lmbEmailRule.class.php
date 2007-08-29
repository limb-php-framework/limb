<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
 * Checks that field value is a valid Email address.
 * @package validation
 * @version $Id: lmbEmailRule.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbEmailRule extends lmbDomainRule
{
  function check($value)
  {
    if (is_integer(strpos($value, '@')))
    {
      list($user, $domain) = split('@', $value, 2);
      $this->_checkUser($user);
      $this->_checkDomain($domain);
    }
    else
      $this->error('{Field} must contain a @ character.');
  }

  protected function _checkUser($value)
  {
    if (!preg_match('/^[a-z0-9]+([_.-][a-z0-9]+)*$/i', $value))
        $this->error('Invalid user in {Field}.');
  }

  protected function _checkDomain($value)
  {
    parent :: check($value);
  }
}

