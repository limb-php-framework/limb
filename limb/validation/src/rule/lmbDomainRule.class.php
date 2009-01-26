<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * Checks that field value is a valid domain name.
 * @package validation
 * @version $Id: lmbDomainRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbDomainRule extends lmbSingleFieldRule
{
  function check($value)
  {
    // Check for entirely numberic domains.  Is 666.com valid?
    // Don't check for 2-4 character length on TLD because of things like .local
    // We can't be too restrictive by default.
    if (!preg_match("/^[a-z0-9.-]+$/i", $value))
      $this->error('{Field} must contain only letters, numbers, hyphens, and periods.');

    if (strlen($value) >= 2 && is_integer(strpos($value, '--', 2)))
      $this->error('{Field} may not contain double hyphens (--).');

    if (0 === strpos($value, '.'))
      $this->error('{Field} cannot start with a period.');

    if (strlen($value) -1 === strrpos($value, '.'))
      $this->error('{Field} cannot end with a period.');

    if (strlen($value) >= 2 && is_integer(strpos($value, '..', 2)))
    {
      $this->error('{Field} may not contain double periods (..).');
    }

    $segments = explode('.', $value);
    foreach($segments as $dseg)
    {
      $len = strlen($dseg);
      /* ignore empty segments that're due to other errors */
      if (1 > $len)
          continue;

      if ($len > 63)
      {
        $this->error('{Field} segment {segment} is too large (it must be 63 characters or less).',
                     array('segment' => $dseg));
      }

      if ($dseg{$len-1} == '-' || $dseg{0} == '-')
      {
        $this->error('{Field} segment {segment} may not begin or end with a hyphen.',
                     array('segment' => $dseg));
      }
    }
  }
}

