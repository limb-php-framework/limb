<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementWidget.class.php');
lmb_require('limb/wysiwyg/src/lmbWysiwygConfigurationHelper.class.php');
/**
 * @package macro
 * @version $Id$
 */
class lmbMacroBaseWysiwygWidget extends lmbMacroFormElementWidget
{
  protected $skip_render = array('value','config_name', 'profile_name');
  /**
   * @var lmbWysiwygConfigurationHelper
   */
  protected $_helper;

  function renderWysiwyg()
  {
    echo '<textarea';
    $this->renderAttributes();
    echo '>';
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
    echo '</textarea>';
  }

  function _initWysiwyg()
  {
    $this->_helper = new lmbWysiwygConfigurationHelper();
    $this->_helper->setProfileName($this->getAttribute('profile_name'));

    if(!$this->getAttribute('rows'))
      $this->setAttribute('rows', $this->_helper->getOption('rows'));

    if(!$this->getAttribute('cols'))
      $this->setAttribute('cols', $this->_helper->getOption('cols'));

    if(!$this->getAttribute('width'))
      $this->setAttribute('width', $this->_helper->getOption('width'));

    if(!$this->getAttribute('height'))
      $this->setAttribute('height', $this->_helper->getOption('height'));
  }

}

