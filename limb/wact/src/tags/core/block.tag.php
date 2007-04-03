<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: block.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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

  /**
  * @param WactCodeWriter
  */
  function preGenerate($code_writer)
  {
    parent::preGenerate($code_writer);
    $code_writer->writePHP('if (' . $this->getComponentRefCode() . '->isVisible()) {');
  }

  /**
  * @param WactCodeWriter
  */
  function postGenerate($code_writer)
  {
    $code_writer->writePHP('}');
    parent::postGenerate($code_writer);
  }
}
?>