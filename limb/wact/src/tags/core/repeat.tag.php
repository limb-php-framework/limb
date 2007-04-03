<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: repeat.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag core:REPEAT
* @req_attributes value
*/
class WactCoreRepeatTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $counter = '$' . $code->getTempVariable();

    $value_attr = $this->attributeNodes['value'];

    if($value_attr->isConstant())
    {
      $value = $value_attr->getValue();
      $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');
    }
    else
    {
      $value = '$' . $code->getTempVariable();
      $code->writePHP($value . ' = ');
      $value_attr->generateExpression($code);
      $code->writePHP(';');

      $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');
    }

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>