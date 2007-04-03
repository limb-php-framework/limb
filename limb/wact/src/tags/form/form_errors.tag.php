<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_errors.tag.php 5223 2007-03-13 13:31:39Z serega $
 * @package    wact
 */

/**
 * @tag form:ERRORS
 * @parent_tag_class WactFormTag
 */
class WactFormErrorsTag extends WactCompilerTag
{
  protected $list_tag;

  function preGenerate($code)
  {
    if($target = $this->getAttribute('target'))
    {
      $form = $this->findParentByClass('WactFormTag');
      $this->list_tag = $form->getChild($target);
    }

    if($list_tag = $this->findChildByClass('WactListListTag'))
      $this->list_tag = $list_tag;

    if(!$this->list_tag)
      $this->raiseCompilerError('Could not find child list tag');

    parent::preGenerate($code);
  }

  function generateContents($code)
  {
    $for = '';
    if($this->hasAttribute('for'))
      $for = $this->getAttribute('for');

    $form = $this->findParentByClass('WactFormTag');

    $code->writePHP($this->list_tag->getComponentRefCode() . '->registerDataSet(' .
                    $form->getComponentRefCode() . '->getErrorsDataSet("' . $for. '"));');

    parent :: generateContents($code);
  }
}
?>
