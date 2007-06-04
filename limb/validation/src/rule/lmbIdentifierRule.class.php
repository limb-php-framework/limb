<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIdentifierRule.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value is an alpha-numeric string
*/
class lmbIdentifierRule extends lmbSingleFieldRule
{
  function check($value)
  {
    $value = "$value";

    if (!preg_match("/^[a-zA-Z0-9.-]+$/i", $value))
        $this->error('{Field} must contain only letters and numbers');
  }
}
?>