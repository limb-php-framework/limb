<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
class lmbWysiwygConfigurationHelper
{
  protected $_config_name = 'wysiwyg';
  protected $_profile_name;
  protected $_wysiwyg_types = array(
    'ckeditor' => array(
      'macro' => array(
        'file' => 'limb/wysiwyg/src/macro/lmbMacroCKEditorWidget.class.php',
        'class' => 'lmbMacroCKEditorWidget'
      )
    ),
    'fckeditor' => array(
      'macro' => array(
        'file' => 'limb/wysiwyg/src/macro/lmbMacroFCKEditorWidget.class.php',
        'class' => 'lmbMacroFCKEditorWidget'
      ),
      'wact' => array(
        'file' => 'limb/wysiwyg/src/wact/lmbFCKEditorComponent.class.php',
        'class' => 'lmbFCKEditorComponent'
      ),
    ),
    'tinymce' => array(
      'macro' => array(
        'file' => 'limb/wysiwyg/src/macro/lmbMacroTinyMCEWidget.class.php',
        'class' => 'lmbMacroTinyMCEWidget'
      ),
      'wact' => array(
        'file' => 'limb/wysiwyg/src/wact/lmbTinyMCEComponent.class.php',
        'class' => 'lmbTinyMCEComponent'
      )
    ),
  );
  
  function getWysiwygConfigOption($name)
  {
    return lmbToolkit::instance()->getConf($this->_config_name)->get($name);
  }
  
  function getProfileName()
  {
    if($this->_profile_name)
      return ($this->_profile_name);
    else 
      return $this->getWysiwygConfigOption('default_profile');
  }
  
  function setProfileName($name)
  {
    $this->_profile_name = $name;
  }
  
  function getOption($name)
  {
    $profile_options = $this->getWysiwygConfigOption($this->getProfileName());
    if(isset($profile_options[$name]))
      return $profile_options[$name];
  }
  
  function getMacroWidgetInfo()
  {  
    $wysiwyg_type = $this->getOption('type');
    
    if(!isset($this->_wysiwyg_types[$wysiwyg_type]))
      throw new lmbException('Wysiwyg type "'.$wysiwyg_type.'" not supported',array('type' => $wysiwyg_type));
      
    return $this->_wysiwyg_types[$wysiwyg_type]['macro'];
  }  
  
  function getWactWidgetInfo()
  {  
    $wysiwyg_type = $this->getOption('type');
    
    if(!isset($this->_wysiwyg_types[$wysiwyg_type]))
      throw new lmbException('Wysiwyg type "'.$wysiwyg_type.'" not supported',array('type' => $wysiwyg_type));
      
    return $this->_wysiwyg_types[$wysiwyg_type]['wact'];
  }  
}