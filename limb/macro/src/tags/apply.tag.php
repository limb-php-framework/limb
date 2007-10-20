<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * @tag apply
 * @package macro
 * @version $Id$
 */
class lmbMacroApplyTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $name = $this->get('template');

    $args = $this->_attributesIntoArray();

    $arg_str = 'array(';
    foreach($args as $key => $value)
      $arg_str .= "'$key' => $value,";
    $arg_str .= ')';

    $code->writePHP('$this->_template'. $name . '(' . $arg_str . ');');
  }

  protected function _attributesIntoArray()
  {
    $arr = array();
    foreach($this->attributes as $k => $attribute)
      $arr[$k] = $this->getEscaped($k);
    return $arr;
  }
}

