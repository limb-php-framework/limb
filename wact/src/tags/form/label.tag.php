<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for building runtime form labels
 * @tag label
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle
 * @restrict_self_nesting
 * @runat client
 * @parent_tag_class WactFormTag
 * @package wact
 * @version $Id: label.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactLabelTag extends WactRuntimeComponentHTMLTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactLabelComponent.class.php';
  protected $runtimeComponentName = 'WactLabelComponent';

  /**
   * @param WactCodeWriter
   */
  function generateConstructor($code_writer)
  {
    parent::generateConstructor($code_writer);

    if ($this->hasAttribute('errorclass'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->errorclass = ');
      $code_writer->writePHPLiteral($this->getAttribute('errorclass'));
      $code_writer->writePHP(';');
    }

    if ($this->hasAttribute('errorstyle'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->errorstyle = ');
      $code_writer->writePHPLiteral($this->getAttribute('errorstyle'));
      $code_writer->writePHP(';');
    }
  }
}

