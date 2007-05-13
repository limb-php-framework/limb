<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: optional.tag.php 5878 2007-05-13 11:14:57Z serega $
 * @package    wact
 */

/**
 * The opposite of the CoreDefaultTag
 * @tag core:OPTIONAL
 * @req_attributes for
 * @convert_to_expression for
 */
class WactCoreOptionalTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $tempvar = $code->getTempVariable();
    $code->writePHP('$' . $tempvar . ' = ');
    $this->attributeNodes['for']->generateExpression($code);
    $code->writePHP(';');

    $code->writePHP('if (is_scalar($' . $tempvar .' )) $' . $tempvar . '= trim($' . $tempvar . ');');
    $code->writePHP('if (!empty($' . $tempvar . ')){');

    parent :: generateTagContent($code);

    $code->writePHP('}');
  }
}
?>
