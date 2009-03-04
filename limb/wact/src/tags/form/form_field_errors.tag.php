<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag form:FIELD_ERRORS
 * @parent_tag_class WactFormTag
 * @package wact
 * @version $Id$
 */
class WactFormFieldErrorsTag extends WactCompilerTag
{
  function generateBeforeContent($code)
  {
    $for = '';
    if($this->hasAttribute('for'))
      $for = $this->getAttribute('for');

    $form = $this->findParentByClass('WactFormTag');

    if(!$list_tag = $this->findChildByClass('WactListListTag'))
      $this->raiseCompilerError('Could not find child list tag');

    $code->writePHP($list_tag->getComponentRefCode() . '->registerDataSet(' .
                    $form->getComponentRefCode() . '->getFieldErrorsDataSet("' . $for. '"));');
  }
}

