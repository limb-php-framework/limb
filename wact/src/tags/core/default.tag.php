<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Output a portion of the template if DBE property has a value at runtime
 * @tag core:DEFAULT
 * @req_attributes for
 * @convert_to_expression for
 * @package wact
 * @version $Id: default.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCoreDefaultTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $tempvar = $code->getTempVariable();
    $code->writePHP('$' . $tempvar . ' = ');
    $this->attributeNodes['for']->generateExpression($code);
    $code->writePHP(';');

    $code->writePHP('if (is_scalar($' . $tempvar .' )) $' . $tempvar . ' = trim($' . $tempvar . ');');
    $code->writePHP('if (empty($' . $tempvar . ')) {');

    parent :: generateTagContent($code);

    $code->writePHP('}');
  }
}

