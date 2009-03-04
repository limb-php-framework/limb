<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 /**
 * class Zend_Exception
 *
 * @package zfsearch
 * @version $Id$
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

//quite a dirty hack, probably this should be made more isolated from Limb3...
class Zend_Exception extends lmbException {}

