<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * Macro analog for html <input type="image"> tag
 * @tag form:image
 * @forbid_end_tag
 * @package macro
 */
class lmbMacroFormImageTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'input';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroInputWidget.class.php';
  protected $widget_class_name = 'lmbMacroInputWidget';

  function preParse($compiler)
  {
    parent :: preParse($compiler);
    $this->set('type', 'image');
  }
}

