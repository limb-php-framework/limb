<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/js/src/lmbJsPreprocessor.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class CustomDirectiveProcessor
{
  static function processDefine($name, $value)
  {
    return "var $name = $value;";
  }
}

class lmbJsPreprocessorTest extends UnitTestCase
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

  function testIncludeOneFile()
  {
    $this->_createFile($file = 'foo.js',
<<<EOD
//#include "bar.js"
foo
EOD
);
    $this->_createFile('bar.js',
<<<EOD
bar
EOD
);

    $builder = new lmbJsPreprocessor();
    $this->assertEqual($builder->processFile($this->_getFile($file)),
<<<EOD
bar
foo
EOD
);
  }

  function testIncludeSeveralFiles()
  {
    $this->_createFile($file1 = 'foo.js',
<<<EOD
//#include "bar.js"
//#include "wow.js"
foo
EOD
);

    $this->_createFile($file2 = 'bar.js',
<<<EOD
bar
EOD
);

    $this->_createFile($file3 = 'wow.js',
<<<EOD
//#include "bar.js"
wow
EOD
);


    $builder = new lmbJsPreprocessor();
    $this->assertEqual($builder->processFiles(array($this->_getFile($file1), $this->_getFile($file1))),
<<<EOD
bar
wow
foo

EOD
);
  }

  function testCyclicInclude()
  {
    $this->_createFile($file = 'foo.js',
<<<EOD
//#include "bar.js"
foo
EOD
);

    $this->_createFile('bar.js',
<<<EOD
//#include "foo.js"
bar
EOD
);

    $builder = new lmbJsPreprocessor();
    $this->assertEqual($builder->processFile($this->_getFile($file)),
<<<EOD
bar
foo
EOD
);
  }

  function testDeeperInclude()
  {
    $this->_createFile($file = 'foo.js',
<<<EOD
//#include "bar.js"
//#include "zoo.js"
foo
EOD
);

    $this->_createFile('bar.js',
<<<EOD
//#include "baz.js"
bar
EOD
);

    $this->_createFile('baz.js',
<<<EOD
baz
EOD
);

    $this->_createFile('zoo.js',
<<<EOD
zoo
EOD
);

    $builder = new lmbJsPreprocessor();
    $this->assertEqual($builder->processFile($this->_getFile($file)),
<<<EOD
baz
bar
zoo
foo
EOD
);
  }

  function testIncludeWithWildcards()
  {
    $this->_createFile($file = 'foo.js',
<<<EOD
//#include "*.js"
foo
EOD
);

    $this->_createFile('bar.js',
<<<EOD
bar
EOD
);

    $this->_createFile('baz.js',
<<<EOD
baz
EOD
);

    $this->_createFile('zoo.js',
<<<EOD
zoo
EOD
);

    $builder = new lmbJsPreprocessor();
    $this->assertEqual($builder->processFile($this->_getFile($file)),
<<<EOD
bar
baz
zoo
foo
EOD
);
  }

  function testProcessCustomDirective()
  {
    $this->_createFile($file = 'foo.js',
<<<EOD
//#define wow "hey"
foo
EOD
);

    $builder = new lmbJsPreprocessor();
    $builder->addDirective('define', array('CustomDirectiveProcessor', 'processDefine'));

$expected = <<<EOD
var wow = "hey";
foo
EOD;
    $this->assertEqual($builder->processFile($this->_getFile($file)), $expected);
  }

  protected function _getFile($file) {
    return lmb_env_get('LIMB_VAR_DIR') . '/' . $file;
  }

  protected function _createFile($file, $content)
  {
    $file = $this->_getFile($file);

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


