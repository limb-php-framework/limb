<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmailRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
* Checks that field value is a valid Email address.
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
      $this->error(lmb_i18n('{Field} must contain a @ character.', 'validation'));
  }

  protected function _checkUser($value)
  {
    if (!preg_match('/^[a-z0-9]+([_.-][a-z0-9]+)*$/i', $value))
        $this->error(lmb_i18n('Invalid user in {Field}.', 'validation'));
  }

  protected function _checkDomain($value)
  {
    parent :: check($value);
  }
}
?>