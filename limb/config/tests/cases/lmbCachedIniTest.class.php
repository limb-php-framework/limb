<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIniTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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