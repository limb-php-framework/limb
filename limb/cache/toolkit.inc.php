<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package cache
 * @version $Id: toolkit.inc.php 5969 2007-06-08 10:51:09Z pachanga $
 */
lmb_require('limb/cache/src/lmbCacheTools.class.php');
lmbToolkit :: merge(new lmbCacheTools());

?>