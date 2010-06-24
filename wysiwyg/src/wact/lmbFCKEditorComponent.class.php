<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/wysiwyg/src/wact/lmbWysiwygComponent.class.php');

@define('LIMB_FCKEDITOR_DIR', 'limb/wysiwyg/lib/FCKeditor/');

/**
 * class lmbFCKEditorComponent.
 *
 * @package wysiwyg
 * @version $Id: lmbFCKEditorComponent.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbFCKEditorComponent extends lmbWysiwygComponent
{
  var $dir = '';

  function renderContents()
  {
    $this->renderEditor();
  }

  function renderEditor()
  {
    include_once(LIMB_FCKEDITOR_DIR . '/fckeditor.php');

    $editor = new FCKeditor($this->getAttribute('name')) ;
    $this->_setEditorParameters($editor);
    $editor->Value = $this->getValue();

    $editor->Create();
  }

  function _setEditorParameters($editor)
  {
    if($this->_helper->getOption('base_path'))
      $editor->BasePath	= $this->_helper->getOption('base_path');
    else
      $editor->BasePath = '/shared/wysiwyg/fckeditor/';

    if($this->_helper->getOption('Config'))
      $editor->Config	= $this->_helper->getOption('Config');    

    if($this->_helper->getOption('ToolbarSet'))
      $editor->ToolbarSet	= $this->_helper->getOption('ToolbarSet');

    $editor->Width = $this->getAttribute('width');
    $editor->Height = $this->getAttribute('height');
  }
}


