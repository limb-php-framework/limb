<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2010 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package mail
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/mail/src/toolkit/lmbMailTools.class.php');
lmbToolkit :: merge(new lmbMailTools());