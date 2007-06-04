<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUTF8MbstringDriverTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/i18n/src/charset/lmbUTF8MbstringDriver.class.php');
lmb_require(dirname(__FILE__) . '/lmbMultiByteStringDriverTestBase.class.php');

class lmbUTF8MbstringDriverTest extends lmbMultiByteStringDriverTestBase
{
    function _createDriver() {
        if(!function_exists('mb_strlen'))
            return null;

        return new lmbUTF8MbstringDriver();
    }
}

?>