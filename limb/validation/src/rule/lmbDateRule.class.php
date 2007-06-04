<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
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

  function __construct($field_name, $type = lmbDateRule :: TYPE_ISO, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->type = $type;
  }

  function check($value)
  {
    if($this->type == lmbDateRule :: TYPE_ISO)
    {
      if(!lmbDate :: validate((string)$value))
        $this->error('{Field} is not a valid ISO formatted date(YYYY-MM-DD HH:MM).');
    }
  }
}
?>