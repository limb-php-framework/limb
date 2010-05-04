<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
 * Checks that field value is a valid Email address.
 * @package validation
 * @version $Id: lmbEmailRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbEmailRule extends lmbDomainRule
{
  function check($value)
  {
    if (is_integer(strpos($value, '@')))
    {
      list($user, $domain) = explode('@', $value);
      $this->_checkUser($user);
      $this->_checkDomain($domain);
    }
    else
      $this->error('{Field} must contain a @ character.');
  }

  function error($message, $values = array(), $i18n_params = array())
  {
    if(isset($this->custom_error))
    {
      if($this->is_valid)
        parent::error($message, $values = array(), $i18n_params = array());
    }
    else
      parent::error($message, $values = array(), $i18n_params = array());
  }

  protected function _checkUser($value)
  {
    if (!preg_match('/^[a-z0-9._%+-]+$/i', $value))
        $this->error('Invalid user in {Field}.');
  }

  protected function _checkDomain($value)
  {
    parent :: check($value);
  }
}
