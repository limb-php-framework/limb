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
  function preGenerate($code)
  {
    parent::preGenerate($code);

    $this->attributeNodes['exp']->generatePreStatement($code);

    $code->writePHP('if (');
    $code->writePHP($this->attributeNodes['exp']->generateExpression($code));
    $code->writePHP('){');
  }

  function postGenerate($code)
  {
    $code->writePHP('}');
    parent::postGenerate($code);

    $this->attributeNodes['exp']->generatePostStatement($code);
  }
}
?>
