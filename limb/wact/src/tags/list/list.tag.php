<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: list.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */


/**
 * The parent compile time component for lists
 * @tag list:LIST
 */
class WactListListTag extends WactRuntimeComponentTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/list/WactListComponent.class.php';
  protected $runtimeComponentName = 'WactListComponent';

  function generateTagContent($code_writer)
  {
    if ($this->hasAttribute('from'))
    {
      $this->generateDereference($code_writer, $this->getAttribute('from'));
    }

    $code_writer->writePHP($this->getComponentRefCode() . '->rewind();' . "\n");
    $code_writer->writePHP('if (' . $this->getComponentRefCode() . '->valid()) {' . "\n");

    parent :: generateTagContent($code_writer);

    $code_writer->writePHP('}' . "\n");

    $emptyChild = $this->findImmediateChildByClass('WactListDefaultTag');
    if ($emptyChild)
    {
      $code_writer->writePHP(' else { ' . "\n");
      $emptyChild->generateNow($code_writer);
      $code_writer->writePHP('}' . "\n");
    }
  }

  function generateDereference($code_writer, $from)
  {
    $from_dbe = new WactDataBindingExpressionNode($from, $this->parent);
    $from_dbe->generatePreStatement($code_writer);

    $code_writer->writePHP($this->getComponentRefCode() . '->registerDataset(');

    $from_dbe->generateExpression($code_writer);

    $code_writer->writePHP(');' . "\n");

    $from_dbe->generatePostStatement($code_writer);
  }
}
?>