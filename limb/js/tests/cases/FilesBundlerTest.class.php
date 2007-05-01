<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    js
 */
lmb_require('limb/js/src/lmbFilesBundler.class.php');
lmb_require('limb/js/src/lmbJsDependencyExtractor.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class BundleStubFilter
{
  function BundleStubFilter($wrap)
  {
    $this->wrap = $wrap;
  }

  function apply(&$content)
  {
    $content = $this->wrap . $content . $this->wrap;
  }
}

class FilesBundlerTest extends UnitTestCase
{
  var $created_files = array();

  function setUp()
  {
    $this->_cleanup();
  }

  function tearDown()
  {
    $this->_cleanup();
  }

  function testSimpleBundle()
  {
    $this->_createFile($root_file = LIMB_VAR_DIR . '/foo.js',
<<<EOD
Limb.require('bar');
foo
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/bar.js',
<<<EOD
bar

EOD
);

    $bundler = new lmbFilesBundler(new lmbJsDependencyExtractor(LIMB_VAR_DIR));
    $bundler->addRootFile($root_file);
    $this->assertEqual($bundler->createBundle(),
<<<EOD
bar
foo

EOD
);
  }

  function testCreateBundleFile()
  {
    $this->_createFile($root_file = LIMB_VAR_DIR . '/foo.js',
<<<EOD
Limb.require('bar');
foo
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/bar.js',
<<<EOD
bar
EOD
);

    $bundle_file = LIMB_VAR_DIR . '/bundle';

    $bundler = new lmbFilesBundler(new lmbJsDependencyExtractor(LIMB_VAR_DIR));
    $bundler->addRootFile($root_file);
    $bundler->createBundleFile($bundle_file);
    $this->assertEqual(file_get_contents($bundle_file),
<<<EOD
bar
foo

EOD
);
    unlink($bundle_file);
  }

  function testBundleSeveralRoots()
  {
    $this->_createFile($root_file1 = LIMB_VAR_DIR . '/foo.js',
<<<EOD
Limb.require('bar');
foo
EOD
);

    $this->_createFile($root_file2 = LIMB_VAR_DIR . '/bar.js',
<<<EOD
bar
EOD
);

    $bundler = new lmbFilesBundler(new lmbJsDependencyExtractor(LIMB_VAR_DIR));
    $bundler->addRootFile($root_file1);
    $bundler->addRootFile($root_file2);
    $this->assertEqual($bundler->createBundle(),
<<<EOD
bar
foo


EOD
);
  }

  function testCyclicBundle()
  {
    $this->_createFile($root_file = LIMB_VAR_DIR . '/foo.js',
<<<EOD
Limb.require('bar');
foo
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/bar.js',
<<<EOD
Limb.require('foo');
bar
EOD
);

    $bundler = new lmbFilesBundler(new lmbJsDependencyExtractor(LIMB_VAR_DIR));
    $bundler->addRootFile($root_file);
    $this->assertEqual($bundler->createBundle(),
<<<EOD
bar
foo

EOD
);
  }

  function testDeeperBundle()
  {
    $this->_createFile($root_file = LIMB_VAR_DIR . '/foo.js',
<<<EOD
Limb.require('bar');
Limb.require('zoo');
foo
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/bar.js',
<<<EOD
Limb.require('baz');
bar
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/baz.js',
<<<EOD
baz
EOD
);

    $this->_createFile(LIMB_VAR_DIR . '/zoo.js',
<<<EOD
zoo
EOD
);

    $bundler = new lmbFilesBundler(new lmbJsDependencyExtractor(LIMB_VAR_DIR));
    $bundler->addRootFile($root_file);
    $this->assertEqual($bundler->createBundle(),
<<<EOD
baz
bar
zoo
foo

EOD
);
  }

  function testApplyFilters()
  {
    $root_file = LIMB_VAR_DIR . '/foo.js';
    $this->_createFile($root_file, $content = "foo");

    $bundler = new lmbFilesBundler();
    $bundler->addRootFile($root_file);

    $f1 = new BundleStubFilter('--');
    $f2 = new BundleStubFilter('||');

    $bundler->addFilter($f1);
    $bundler->addFilter($f2);

    $this->assertEqual($bundler->createBundle(), "||--foo\n--||");
  }

  protected function _createFile($file, $content)
  {
    lmbFs :: mkdir(dirname($file));

    $fh = fopen($file, 'w');
    fwrite($fh, $content);
    fclose($fh);
    $this->created_files[] = $file;
  }

  protected function _cleanup()
  {
    foreach($this->created_files as $file)
      unlink($file);

    $this->created_files = array();
  }
}

?>
