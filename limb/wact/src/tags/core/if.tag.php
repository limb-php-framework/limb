<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

/**
 * Inserts an IF statement into compiled template
 * @tag core:IF
 * @req_attributes exp
 */
class WactCoreIfTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePHP('if (');
    $code->writePHP($this->attributeNodes['exp']->generateExpression($code));
    $code->writePHP('){');

    parent :: generateTagContent($code);

    $code->writePHP('}');
  }
}
?>
