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
 * @version $Id: common.inc.php 6221 2007-08-07 07:24:35Z pachanga $
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/web_app/common.inc.php');
lmb_require('limb/cms/toolkit.inc.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');
if(defined('LIMB_HTTP_GATEWAY_PATH'))
  lmbCmsNode :: setGatewayPath(LIMB_HTTP_GATEWAY_PATH);


