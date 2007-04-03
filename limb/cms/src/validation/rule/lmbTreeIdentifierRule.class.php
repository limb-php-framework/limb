<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */

/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbTreeIdentifierRule extends lmbSingleFieldRule
{
  protected $parent_node_id_field_name;

  function check($value)
  {
    if(!preg_match('~^[a-zA-Z0-9-_\.]+$~', $value))
    {
      $this->error(lmb_i18n('{Field} can contain numeric, latin alphabet and `-`, `_`, `.` symbols only', 'cms'));
      return;
    }
  }
}

?>