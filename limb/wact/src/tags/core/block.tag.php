<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: block.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* Compile time component for block tags
* @tag core:BLOCK
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
?>