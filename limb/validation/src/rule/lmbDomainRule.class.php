<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDomainRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value is a valid domain name.
*/
class lmbDomainRule extends lmbSingleFieldRule
{
  function check($value)
  {
    // Check for entirely numberic domains.  Is 666.com valid?
    // Don't check for 2-4 character length on TLD because of things like .local
    // We can't be too restrictive by default.
    if (!preg_match("/^[a-z0-9.-]+$/i", $value))
      $this->error(lmb_i18n('{Field} must contain only letters, numbers, hyphens, and periods.', 'validation'));

    if (strlen($value) >= 2 && is_integer(strpos($value, '--', 2)))
        $this->error(lmb_i18n('{Field} may not contain double hyphens (--).', 'validation'));

    if (0 === strpos($value, '.'))
        $this->error(lmb_i18n('{Field} cannot start with a period.', 'validation'));

    if (strlen($value) -1 === strrpos($value, '.'))
        $this->error(lmb_i18n('{Field} cannot end with a period.', 'validation'));

    if (strlen($value) >= 2 && is_integer(strpos($value, '..', 2)))
        $this->error(lmb_i18n('{Field} may not contain double periods (..).', 'validation'));

    $segments = explode('.', $value);
    foreach($segments as $dseg)
    {
      $len = strlen($dseg);
      /* ignore empty segments that're due to other errors */
      if (1 > $len) {
          continue;
      }
      if ($len > 63) {
          $this->error(lmb_i18n('{Field} segment {segment} is too large (it must be 63 characters or less).', 'validation'),
                       array('segment' => $dseg));
      }
      if ($dseg{$len-1} == '-' || $dseg{0} == '-') {
          $this->error(lmb_i18n('{Field} segment {segment} may not begin or end with a hyphen.', 'validation'),
                       array('segment' => $dseg));
      }
    }
  }
}
?>