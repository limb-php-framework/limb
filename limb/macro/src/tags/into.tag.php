<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

//temporary includes, make it more flexible later
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');

lmbMacroTagDictionary :: instance()->register(new lmbMacroTagInfo('into', 'lmbMacroIntoTag'), __FILE__);

/**
 * class lmbMacroIntoTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroIntoTag extends lmbMacroTag
{
}

