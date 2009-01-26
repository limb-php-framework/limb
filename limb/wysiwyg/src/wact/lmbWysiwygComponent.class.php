<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/wact/src/components/form/form.inc.php');

/**
 * class lmbWysiwygComponent.
 *
 * @package wysiwyg
 * @version $Id: lmbWysiwygComponent.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbWysiwygComponent extends WactTextAreaComponent
{
  /**
   * @var lmbWysiwygConfigurationHelper
   */
  protected $_helper;

  function renderContents()
  {
    echo '<textarea';
    $this->renderAttributes();
    echo '>';
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
    echo '</textarea>';
  }

  function initWysiwyg($profile_name)
  {
    $this->_helper = new lmbWysiwygConfigurationHelper();
    $this->_helper->setProfileName($profile_name);
       
    $this->group = $group;

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

