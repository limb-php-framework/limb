<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppTools.class.php 5286 2007-03-20 08:31:30Z serega $
 * @package    web_app
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

@define('LIMB_TEMPLATES_INCLUDE_PATH', 'template;limb/*/template');
@define('LIMB_WACT_TAGS_INCLUDE_PATH', 'src/template/tags;limb/*/src/template/tags;limb/wact/src/tags');

class lmbViewTools extends lmbAbstractTools
{
  protected $wact_locator;

  function getWactLocator()
  {
    if(is_object($this->wact_locator))
      return $this->wact_locator;

    lmb_require('limb/view/src/wact/lmbWactTemplateLocator.class.php');

    $locator = $this->toolkit->getFileLocator(LIMB_TEMPLATES_INCLUDE_PATH, 'template');
    $this->wact_locator = new lmbWactTemplateLocator($locator, LIMB_VAR_DIR . '/compiled/');

    return $this->wact_locator;
  }

  function setWactLocator($wact_locator)
  {
    $this->wact_locator = $wact_locator;
  }
}
?>
