<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package net
 * @version $Id: toolkit.inc.php 7417 2008-12-19 07:25:26Z conf $
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/net/src/lmbNetTools.class.php');
lmbToolkit :: merge(new lmbNetTools());


