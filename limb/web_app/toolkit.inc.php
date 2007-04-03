<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5372 2007-03-28 09:06:30Z pachanga $
 * @package    web_app
 */
lmb_require('limb/net/toolkit.inc.php');
lmb_require('limb/i18n/toolkit.inc.php');
lmb_require('limb/config/toolkit.inc.php');
lmb_require('limb/file_schema/toolkit.inc.php');
lmb_require('limb/view/toolkit.inc.php');

lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/web_app/src/toolkit/lmbWebAppTools.class.php');
lmbToolkit :: merge(new lmbWebAppTools());

?>