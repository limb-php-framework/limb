<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for block tags
 * @tag core:BLOCK
 * @package wact
 * @version $Id: block.tag.php 7686 2009-03-04 19:57:12Z korchasa $
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

