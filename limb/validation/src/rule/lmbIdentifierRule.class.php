<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIdentifierRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
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
        $this->error(lmb_i18n('{Field} must contain only letters and numbers', 'validation'));
  }
}
?>