<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy 2004-2012 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

lmb_require('limb/wysiwyg/src/macro/lmbMacroBaseWysiwygWidget.class.php');
lmb_env_setor('LIMB_CKEDITOR_DIR', 'limb/wysiwyg/lib/CKeditor/');
/**
* @package wysiwyg
* @version $Id$
*/
class lmbMacroCKEditorWidget extends lmbMacroBaseWysiwygWidget
{
  function renderWysiwyg()
  {
    $this->_initWysiwyg();

    $this->_renderEditor();
  }

  protected function _renderEditor()
  {
    include_once(lmb_env_get('LIMB_CKEDITOR_DIR') . '/ckeditor.php');

    $editor = new CKeditor();

    if($this->_helper->getOption('basePath'))
      $editor->basePath   = $this->_helper->getOption('basePath');
    else
      $editor->basePath = '/shared/wysiwyg/ckeditor/';

    $config = array();
    if($this->_helper->getOption('Config'))
      $config   = $this->_helper->getOption('Config');

    $editor->editor($this->getAttribute('name'), $this->getValue(), $config);
  }
}