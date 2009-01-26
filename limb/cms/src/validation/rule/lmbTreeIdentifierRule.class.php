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
 * class lmbTreeIdentifierRule.
 *
 * @package cms
 * @version $Id$
 */
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


