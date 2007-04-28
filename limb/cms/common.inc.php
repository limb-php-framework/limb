<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5779 2007-04-28 08:29:21Z wiliam $
 * @package    cms
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/web_app/common.inc.php');
lmb_require('limb/cms/toolkit.inc.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');
if(defined('LIMB_HTTP_GATEWAY_PATH'))
  lmbCmsNode :: setGatewayPath(LIMB_HTTP_GATEWAY_PATH);

?>