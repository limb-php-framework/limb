<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag form:ERRORS
 * @parent_tag_class WactFormTag
 * @package wact
 * @version $Id: form_errors.tag.php 7686 2009-03-04 19:57:12Z korchasa $
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

