<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5415 2007-03-29 10:14:35Z pachanga $
 * @package    i18n
 */
require_once('limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/toolkit.inc.php');

function lmb_i18n($text, $arg1 = null, $arg2 = null)
{
  static $toolkit;

  if(!$toolkit)
    $toolkit = lmbToolkit :: instance();

  return $toolkit->translate($text, $arg1, $arg2);
}

?>
