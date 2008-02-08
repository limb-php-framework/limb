<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag apply
 * @req_attributes template
 * @package macro
 * @version $Id$
 */
class lmbMacroApplyTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $name = $this->get('template');

    $arg_str = $this->attributesIntoArrayString();

    $code->writePHP('$this->_template'. $name . '(' . $arg_str . ');');
  }
}

