<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for block tags
 * @tag core:BLOCK
 * @package wact
 * @version $Id: block.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactCoreBlockTag extends WactRuntimeComponentTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/core/WactBlockComponent.class.php';
  protected $runtimeComponentName = 'WactBlockComponent';

  /**
  * @param WactCodeWriter
  */
  function generateConstructor($code_writer)
  {
    parent::generateConstructor($code_writer);
    if ($this->getBoolAttribute('hide'))
      $code_writer->writePHP($this->getComponentRefCode() . '->hide();'."\n");
  }

  function generateTagContent($code_writer)
  {
    $code_writer->writePHP('if (' . $this->getComponentRefCode() . '->isVisible()) {');

    parent :: generateTagContent($code_writer);

    $code_writer->writePHP('}');
  }
}

