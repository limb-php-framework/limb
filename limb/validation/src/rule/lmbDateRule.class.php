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
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/datetime/src/lmbDate.class.php');

/**
* Checks that field value is a valid date
*/
class lmbDateRule extends lmbSingleFieldRule
{
  const TYPE_ISO = 1;

  protected $type;

  function __construct($name, $type = lmbDateRule :: TYPE_ISO)
  {
    parent :: __construct($name);
    $this->type = $type;
  }

  function check($value)
  {
    if($this->type == lmbDateRule :: TYPE_ISO)
    {
      try
      {
        new lmbDate((string)$value);
      }
      catch(lmbException $e)
      {
        $this->error(lmb_i18n('{Field} is not valid ISO format date(YYYY-MM-DD HH:MM).', 'validation'));
      }
    }
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