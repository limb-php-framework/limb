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
 * @version $Id: common.inc.php 6221 2007-08-07 07:24:35Z pachanga $
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/view/wact.inc.php');
lmb_require('limb/i18n/common.inc.php');

lmb_require_optional('limb/dbal/common.inc.php');
lmb_require_optional('limb/active_record/common.inc.php');

lmb_require(dirname(__FILE__) . '/toolkit.inc.php');
lmb_require(dirname(__FILE__) . '/http.inc.php');


