<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInvalidValueRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value is not equal some invalid value
* Example of usage:
* <code>
* lmb_require('limb/validation/src/rule/lmbInvalidValueRule.class.php');
* $validator->addRule(new lmbInvalidValueRule("region", -1));
* </code>
*/

class lmbInvalidValueRule extends lmbSingleFieldRule
{
  protected $invalid_value;

  function __construct($field_name, $invalid_value)
  {
    parent :: __construct($field_name);

    $this->invalid_value = $invalid_value;
  }

  function check($value)
  {
    $invalid_value = $this->invalid_value;

    settype($invalid_value, 'string');//???

    if ($value == $invalid_value)
    {
      $this->error(lmb_i18n('{Field} value is wrong', 'validation'));
    }
  }
}

?>