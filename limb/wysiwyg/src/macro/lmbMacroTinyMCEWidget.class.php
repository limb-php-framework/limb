<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/wysiwyg/src/macro/lmbMacroBaseWysiwygWidget.class.php');

/**
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroTinyMCEWidget extends lmbMacroBaseWysiwygWidget
{
  protected $_base_path;
  protected $_css_class;
  
  public static $is_included = false;
  
  function renderWysiwyg()
  {
    $this->_initWysiwyg();
    
    $this->_setEditorParameters();
    
    $this->_renderEditor();
  
    parent :: renderWysiwyg();
  }

  protected function _renderEditor()
  {
    if(!self :: $is_included)
    {
      echo '<script language="javascript" type="text/javascript" src="' . $this->_base_path . 'tiny_mce.js"></script>';
      self :: $is_included = true;
    }
    echo '
    <script language="javascript" type="text/javascript">
    tinyMCE.init({
    '.$this->_renderEditorParameters().'
    });
    </script>
    ';
  }

  protected function _renderEditorParameters()
  {
    $items = array();

    $items[] = 'editor_selector : "' . $this->_css_class . '"';

    if ($config = $this->getIniOption('editor') and count($config))
    {
      foreach ($config as $key => $val)
        $items[] = $key . ': "'. $val . '"';
    }

    return implode (",\n", $items);
  }

  protected function _setEditorParameters()
  {
    if($this->getIniOption('base_path'))
      $this->_base_path  = $this->getIniOption('base_path');

    if (!$this->_css_class = $this->getAttribute('class'))
    {
      $this->_css_class = $this->getAttribute('name');
      $this->setAttribute('class', $this->_css_class);
    }
  }

}
