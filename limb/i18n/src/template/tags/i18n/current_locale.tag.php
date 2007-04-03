<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: current_locale.tag.php 5373 2007-03-28 11:10:40Z pachanga $
 * @package    i18n
 */
/**
* @tag limb:CURRENT_LOCALE
* @req_const_attributes name
*/
class lmbCurrentLocaleTag extends WactCompilerTag
{
  function preGenerate($code)
  {
    parent::preGenerate($code);

    $name = $this->getAttribute('name');
    $code->writePhp('if ("' . $name. '" == lmbToolkit :: instance()->getLocale()) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>