<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5396 2007-03-28 13:27:09Z pachanga $
 * @package    web_app
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/view/wact.inc.php');
lmb_require('limb/i18n/common.inc.php');

if(lmb_is_readable('limb/dbal/common.inc.php'))
  lmb_require('limb/dbal/common.inc.php');

if(lmb_is_readable('limb/active_record/common.inc.php'))
  lmb_require('limb/active_record/common.inc.php');

lmb_require(dirname(__FILE__) . '/toolkit.inc.php');
lmb_require(dirname(__FILE__) . '/http.inc.php');

?>