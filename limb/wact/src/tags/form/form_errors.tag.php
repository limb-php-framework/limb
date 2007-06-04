<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_errors.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * @tag form:ERRORS
 * @parent_tag_class WactFormTag
 */
class WactFormErrorsTag extends WactCompilerTag
{
  protected $list_tag;

  function generateBeforeContent($code)
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

    $for = '';
    if($this->hasAttribute('for'))
      $for = $this->getAttribute('for');

    $form = $this->findParentByClass('WactFormTag');

    $code->writePHP($this->list_tag->getComponentRefCode() . '->registerDataSet(' .
                    $form->getComponentRefCode() . '->getErrorsDataSet("' . $for. '"));');
  }
}
?>
