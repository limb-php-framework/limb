<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5353 2007-03-27 16:20:49Z pachanga $
 * @package    i18n
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/i18n/src/toolkit/lmbI18NTools.class.php');
lmb_require('limb/file_schema/toolkit.inc.php');
lmbToolkit :: merge(new lmbI18NTools());

?>
