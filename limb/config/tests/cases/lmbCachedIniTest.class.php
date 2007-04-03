<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIniTest.class.php 5423 2007-03-29 13:09:55Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require(dirname(__FILE__) . '/lmbIniTest.class.php');

class lmbCachedIniTest extends lmbIniTest
{
  var $cache_dir;

  function setUp()
  {
    parent :: setUp();

    $this->cache_dir = LIMB_VAR_DIR . '/ini/';
    lmbFs :: rm($this->cache_dir);
  }

  function _createIni($contents)
  {
    file_put_contents($file = LIMB_VAR_DIR . '/tmp_ini/' . mt_rand() . '.ini', $contents);
    return new lmbCachedIni($file, $this->cache_dir);
  }
}

?>