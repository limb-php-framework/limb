<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/net/toolkit.inc.php');
lmb_require('limb/i18n/toolkit.inc.php');
lmb_require('limb/config/toolkit.inc.php');
lmb_require('limb/fs/toolkit.inc.php');
lmb_require('limb/view/toolkit.inc.php');
lmb_require('limb/log/toolkit.inc.php');

lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/web_app/src/toolkit/lmbWebAppTools.class.php');
lmbToolkit :: merge(new lmbWebAppTools());

?>