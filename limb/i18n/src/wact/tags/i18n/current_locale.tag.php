<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag limb:CURRENT_LOCALE
 * @req_const_attributes name
 * @package i18n
 * @version $Id: current_locale.tag.php 6241 2007-08-29 05:46:06Z pachanga $
 */
class lmbCurrentLocaleTag extends WactCompilerTag
{
  function generateTagContent($code)
  {

    $name = $this->getAttribute('name');
    $code->writePhp('if ("' . $name. '" == lmbToolkit :: instance()->getLocale()) {');

    parent::generateTagContent($code);

    $code->writePhp('}');
  }
}


