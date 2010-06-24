<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/tags/form/control.inc.php';

/**
 * Compile time component for building runtime select components
 * @tag select
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 * @restrict_self_nesting
 * @runat client
 * @package wact
 * @version $Id: select.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactSelectTag extends WactControlTag
{
  function prepare()
  {
    if ($this->getBoolAttribute('multiple'))
    {
      $this->runtimeIncludeFile = 'limb/wact/src/components/form/WactSelectMultipleComponent.class.php';
      $this->runtimeComponentName = 'WactSelectMultipleComponent';

      // Repetition of ControlTag::prepare but required for special case
      // of SelectMultiple to provide meaningful error messages
      if (!$this->getBoolAttribute('name'))
      {
        if ( $this->getBoolAttribute('id') )
          $this->setAttribute('name',$this->getAttribute('id').'[]'); // Note - appends [] to id value
        else
          $this->raiseRequiredAttributeError('name');
      }

      if (!is_integer(strpos($this->getAttribute('name'), '[]')))
      {
        $this->raiseCompilerError('Array brackets "[]" required in name attribute, e.g. name="foo[]"',
                                  array('name' => $this->getAttribute('name')));
      }
    }
    else
    {
      $this->runtimeIncludeFile = 'limb/wact/src/components/form/WactSelectSingleComponent.class.php';
      $this->runtimeComponentName = 'WactSelectSingleComponent';
    }

    parent::prepare();
  }

  /**
   * Ignore the compiler time contents and generate the contents at run time.
   * @return void
   */
  function generateTagContent($code_writer)
  {
    $writer = new WactCodeWriter();
    foreach($this->getChildren() as $option_tag)
    {
      if(!is_a($option_tag, 'WactCompilerTag'))
        continue;

      $value = $option_tag->getAttribute('value');
      $prepend = $option_tag->getBoolAttribute('prepend');
      $option_tag->generateNow($writer);
      $text = addslashes($writer->getCode());
      $writer->reset();
      if($prepend)
        $code_writer->writePHP($this->getComponentRefCode() . '->prependToChoices("'. $value .'","'. $text.'");');
      else
        $code_writer->writePHP($this->getComponentRefCode() . '->addToChoices("'. $value .'","'. $text.'");');

      if($option_tag->hasAttribute('selected'))
        $code_writer->writePHP($this->getComponentRefCode() . '->addToDefaultSelection("'. $value .'");');
    }

    $code_writer->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}

