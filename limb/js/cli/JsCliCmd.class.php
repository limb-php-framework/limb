<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/js/common.inc.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/js/src/lmbJsPreprocessor.class.php');

/**
 * class JsCliCmd.
 *
 * @package js
 * @version $Id$
 */
class JsCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    $this->help($argv);
  }

  function build($argv)
  {
    $input = new lmbCliInput();
    $input->setMinimumArguments(2);
    $input->read($argv, false);

    $arguments = $input->getArguments();

    if(!$dest_file = array_pop($arguments))
    {
      echo "Error: You must specify output file.\n";
      return 1;
    }

    $src_files = array();
    foreach($arguments as $src_file)
      $src_files[] = realpath($src_file);

    if(empty($src_files))
    {
      echo "Error: You must specify at least one input file.\n";
      return 1;
    }

    $builder = new lmbJsPreprocessor();
    try
    {
      $contents = $builder->processFiles($src_files);
    }
    catch(lmbException $e)
    {
      echo 'Build error: ' . $e->getMessage();
      return 1;
    }

    lmbFs :: safeWrite($dest_file, $contents);

    return 0;
  }

  function help($argv)
  {
    $txt = <<<EOD
Usage:
  js build <input_file> [<input_file> <input_file> ...] <output_file>

  Process directives in all specified javascript files as set of <input_file> into one <output_file>.
  Only one #include directive supported now by default.

Options:
  -h, --help    dysplays this message

EOD;

    echo $txt;
  }
}


