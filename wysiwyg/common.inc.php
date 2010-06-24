<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package wysiwyg
 * @version $Id: common.inc.php 6598 2007-12-07 08:01:45Z pachanga $
 */
require_once('limb/core/common.inc.php');
lmb_package_require('config');
lmb_package_require('macro');

lmb_require('limb/wysiwyg/toolkit.inc.php');

lmb_package_register('wysiwyg', dirname(__FILE__));
