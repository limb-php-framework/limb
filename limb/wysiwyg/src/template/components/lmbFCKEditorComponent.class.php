<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFCKEditorComponent.class.php 5015 2007-02-08 15:38:22Z pachanga $
 * @package    wysiwyg
 */
lmb_require('limb/wysiwyg/src/template/components/lmbWysiwygComponent.class.php');

@define('LIMB_FCKEDITOR_DIR', 'limb/wysiwyg/lib/FCKeditor/');

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
    if($this->getIniOption('base_path'))
      $editor->BasePath	= $this->getIniOption('base_path');
    else
      $editor->BasePath = '/FCKEditor/';

    if($this->getIniOption('Config'))
      $editor->Config	= $this->getIniOption('Config');

    if($this->getIniOption('ToolbarSet'))
      $editor->ToolbarSet	= $this->getIniOption('ToolbarSet');

    $editor->Width = $this->getAttribute('width');
    $editor->Height = $this->getAttribute('height');
  }
}

?>