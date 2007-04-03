<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUTF8IconvDriverTest.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/charset/lmbUTF8IconvDriver.class.php');
lmb_require(dirname(__FILE__) . '/lmbMultiByteStringDriverTestBase.class.php');

class lmbUTF8IconvDriverTest extends lmbMultiByteStringDriverTestBase
{
  function _createDriver() {
      if(!function_exists('iconv_strlen'))
          return null;

      return new lmbUTF8IconvDriver();
  }
}

?>