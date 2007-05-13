<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: current_locale.tag.php 5882 2007-05-13 21:21:58Z serega $
 * @package    i18n
 */
/**
* @tag limb:CURRENT_LOCALE
* @req_const_attributes name
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

?>