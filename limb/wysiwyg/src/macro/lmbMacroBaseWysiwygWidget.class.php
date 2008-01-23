<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementWidget.class.php');

/**
 * @package macro
 * @version $Id$
 */
class lmbMacroBaseWysiwygWidget extends lmbMacroFormElementWidget
{
  protected $skip_render = array('value','profile','ini_name');
  protected $ini;

  function renderWysiwyg()
  {
    echo '<textarea';
    $this->renderAttributes();
    echo '>';
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
    echo '</textarea>';
  }

  function getIniOption($option)
  {
    if(!$this->ini)
      $this->ini = lmbToolkit :: instance()->getConf($this->getAttribute('ini_name'));
    if($this->ini && ($value = $this->ini->getOption($option, $this->getAttribute('profile'))))
      return $value;
    return '';
  }

  function _initWysiwyg()
  {
    if(!$this->getAttribute('rows'))
      $this->setAttribute('rows', $this->getIniOption('rows'));

    if(!$this->getAttribute('cols'))
      $this->setAttribute('cols', $this->getIniOption('cols'));

    if(!$this->getAttribute('width'))
      $this->setAttribute('width', $this->getIniOption('width'));

    if(!$this->getAttribute('height'))
      $this->setAttribute('height', $this->getIniOption('height'));
  }

}

