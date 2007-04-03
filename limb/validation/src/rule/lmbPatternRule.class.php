<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPatternRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value match some regexp.
* In other words this rule triggers validation error if field value doesn't match regexp.
* Example of usage:
* <code>
* lmb_require('limb/validation/src/rule/lmbPatternRule.class.php');
* $validator->addRule(new lmbPatternRule("title", "/^[a-zA-Z0-9.-]+$/i"));
* </code>
*/

class lmbPatternRule extends lmbSingleFieldRule
{
  protected $pattern;

  function __construct($field_name, $pattern)
  {
    parent :: __construct($field_name);

    $this->pattern = $pattern;
  }

  function check($value)
  {
    if (!preg_match($this->pattern, $value))
    {
      $this->error($this->_getMessage());
    }
  }

  protected function _getMessage()
  {
    return lmb_i18n('{Field} value is wrong', 'validation');
  }
}
?>