<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cms
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z korchasa $
 */
require_once('limb/core/common.inc.php');
lmb_package_require('web_app');
lmb_require('limb/cms/toolkit.inc.php');

lmb_env_setor('JQUERY_FILE_URL','/shared/js/js/jquery/v1.2.3.js');
lmb_env_setor('CMS_STATIC_FILES_VERSION', '2');

lmb_package_register('cms', dirname(__FILE__));
