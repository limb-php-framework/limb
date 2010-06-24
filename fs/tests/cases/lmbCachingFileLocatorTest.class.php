<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/lmbFileLocator.class.php');
lmb_require('limb/fs/src/lmbCachingFileLocator.class.php');

Mock :: generate('lmbFileLocator', 'MockFileLocator');

class lmbCachingFileLocatorTest extends UnitTestCase
{
  var $locator;
  var $wrapped_locator;

  function setUp()
  {
    $this->wrapped_locator = new MockFileLocator();

    $this->locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);
    $this->locator->flushCache();

    $this->cache_file = $this->locator->getCacheFile();
  }

  function testLocateCachingFromWrappedLocator()
  {
    $this->wrapped_locator->expectOnce('locate');
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));

    $this->assertEqual($this->locator->locate('path-to-file'), 'located-path-to-file');
  }

  function testLocateCacheHit()
  {
    $this->wrapped_locator->expectOnce('locate');
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));

    $this->locator->locate('path-to-file');

    $this->assertEqual($this->locator->locate('path-to-file'), 'located-path-to-file');
  }

  function testLocaleNotCacheHitOnOtherParams()
  {
    $this->wrapped_locator->expectCallCount('locate', 2);
    $this->wrapped_locator->setReturnValueAt(0, 'locate', 'located-path-to-file1', array('path-to-file', array()));
    $this->wrapped_locator->setReturnValueAt(1, 'locate', 'located-path-to-file2', array('path-to-file', array('param' => 'value')));

    $this->locator->locate('path-to-file');

    $path = $this->locator->locate('path-to-file', array('param' => 'value'));
    $this->assertEqual($path, 'located-path-to-file2');
  }

  function testWriteToCacheOnDestroy()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    unset($this->locator);

    $this->assertTrue(file_exists($this->cache_file));

    $cached_locations = unserialize(file_get_contents($this->cache_file));

    $this->assertEqual($cached_locations, array('path-to-file' => 'located-path-to-file'));

    unlink($this->cache_file);
  }

  function testWriteToCacheOnlyIfChanged()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    unset($this->locator);

    $this->assertTrue(file_exists($this->cache_file));

    $locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);

    unlink($this->cache_file);

    $locator->locate('path-to-file');
    unset($locator);

    $this->assertFalse(file_exists($this->cache_file));
  }

  function testFlushCache()
  {
    $this->wrapped_locator->setReturnValue('locate', 'located-path-to-file', array('path-to-file', array()));
    $this->locator->locate('path-to-file');

    $this->locator->saveCache();
    $this->assertTrue(file_exists($this->cache_file));
    $this->locator->flushCache();
    $this->assertFalse(file_exists($this->cache_file));
  }

  function testLoadFromCache()
  {
    $php = serialize(array("path-to-file" => "located-path-to-file"));
    file_put_contents($this->cache_file, $php);

    $this->wrapped_locator->expectNever('locate');

    $local_locator = new lmbCachingFileLocator($this->wrapped_locator, LIMB_VAR_DIR);

    $this->assertEqual($local_locator->locate('path-to-file'), 'located-path-to-file');

    unlink($this->cache_file);
  }
}


