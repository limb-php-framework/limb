<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/tags/form/control.inc.php');
lmb_require('limb/wysiwyg/src/lmbWysiwygConfigurationHelper.class.php');
define('LIMB_WYSIWYG_DIR', dirname(__FILE__) . '/../../../');

/**
 * @tag richedit,wysiwyg
 * @package wysiwyg
 * @version $Id: wysiwyg.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbWysiwygTag extends WactControlTag
{
  var $runtimeComponentName = 'lmbWysiwygComponent';
  var $runtimeIncludeFile = 'limb/wysiwyg/src/wact/lmbWysiwygComponent.class.php';
  
  /**
   * @var lmbWysiwygConfigurationHelper
   */
  protected $_helper;

  function prepare()
  {
    $this->_helper = new lmbWysiwygConfigurationHelper();
    
    if($profile_name = $this->attributeNodes['profile'])
      $this->_helper->setProfileName($profile_name->getValue());
      
    $this->determineComponent();
  }

  function determineComponent()
  {
    $component_info = $this->_helper->getWactWidgetInfo();
    $this->runtimeIncludeFile = $component_info['file'];
    $this->runtimeComponentName = $component_info['class'];
  }

  protected function _renderOpenTag($code_writer)
  {
  }

  protected function _renderCloseTag($code_writer)
  {
  }

  function generateTagContent($code)
  {
    if(isset($this->attributeNodes['name']) && !$this->attributeNodes['name']->isConstant())
    {
      $code->writePhp($this->getComponentRefCode() . '->setAttribute("name", ');
      $code->writePhp($this->attributeNodes['name']->generateExpression($code));
      $code->writePhp(');' . "\n");
    }
    $code->writePhp($this->getComponentRefCode() . '->initWysiwyg("'.$this->_helper->getProfileName().'" );' . "\n");
    $code->writePhp($this->getComponentRefCode() . '->renderContents();' . "\n");
  }
}

