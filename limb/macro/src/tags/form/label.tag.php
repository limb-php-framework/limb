<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * Macro analog for html <label> tag
 * @tag label
 * @package macro
 * @version $Id$
 */
class lmbMacroLabelTag extends lmbMacroFormElementTag
{
  protected $html_tag = 'label';
  protected $widget_class_name = 'lmbMacroFormLabelWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroFormLabelWidget.class.php';
}

