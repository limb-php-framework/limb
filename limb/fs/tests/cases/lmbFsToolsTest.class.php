<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFsTools.class.php');

class lmbFsToolsTest extends UnitTestCase
{
  /**
   * @var lmbFsTools
   */
  protected $tools;

  function setUp()
  {
    $this->tools = new lmbFsTools();
  }

  function testGetFilesLocator_CacheConditions()
  {
     $old_mode = lmb_env_get('LIMB_APP_MODE');
     $old_var_dir = lmb_env_get('LIMB_VAR_DIR');
     lmb_env_set('LIMB_APP_MODE', 'devel');
     lmb_env_remove('LIMB_VAR_DIR');

     $this->assertIsA($this->tools->getFileLocator('foo','locator1'), 'lmbFileLocator');

     lmb_env_set('LIMB_VAR_DIR', $old_var_dir);
     $this->assertIsA($this->tools->getFileLocator('foo','locator2'), 'lmbFileLocator');

     lmb_env_set('LIMB_APP_MODE', 'production');
     $this->assertIsA($this->tools->getFileLocator('foo','locator3'), 'lmbCachingFileLocator');

     lmb_env_set('LIMB_APP_MODE', $old_mode);
  }

}


