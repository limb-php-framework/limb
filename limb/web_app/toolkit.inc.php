<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package web_app
 * @version $Id: toolkit.inc.php 7365 2008-12-16 15:54:32Z korchasa $
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

if(lmbToolkit::instance()->isWebAppDebugEnabled())
{
  lmb_require('limb/web_app/src/toolkit/lmbProfileTools.class.php');
  lmbToolkit::merge(new lmbProfileTools());
}

