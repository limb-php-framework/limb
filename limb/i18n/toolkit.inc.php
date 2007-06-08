<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: toolkit.inc.php 5969 2007-06-08 10:51:09Z pachanga $
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/i18n/src/toolkit/lmbI18NTools.class.php');
lmb_require('limb/fs/toolkit.inc.php');
lmbToolkit :: merge(new lmbI18NTools());

?>
