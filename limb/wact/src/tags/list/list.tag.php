<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */


/**
 * The parent compile time component for lists
 * @tag list:LIST
 * @convert_to_expression from
 * @package wact
 * @version $Id: list.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactListListTag extends WactRuntimeComponentTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/list/WactListComponent.class.php';
  protected $runtimeComponentName = 'WactListComponent';

  function generateTagContent($code_writer)
  {
    if ($this->hasAttribute('from'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->registerDataset(');
      $this->attributeNodes['from']->generateExpression($code_writer);
      $code_writer->writePHP(');' . "\n");
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
}

