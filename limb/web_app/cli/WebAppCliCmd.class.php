<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class WebAppCliCmd.
 *
 * @package web_app
 * @version $Id$
 */
class WebAppCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    $this->help($argv);
  }

  function create($argv)
  {
    $input = new lmbCliInput();
    $input->setMinimumArguments(1);
    if(!$input->read($argv, false))
    {
      $this->help($argv);
      return 1;
    }

    $dst_dir = $input->getArgument(0);
    if(file_exists($dst_dir))
    {
      echo "Directory or file '$dst_dir' already exists\n";
      return 1;
    }

    echo "Copying skeleton Limb3 WEB_APP application to '$dst_dir'...\n";

    lmbFs :: cp(dirname(__FILE__) . '/../skel', $dst_dir, '~^\.svn~');

    echo "Generating code from templates...\n";

    $this->_resolveTemplate("$dst_dir/setup.override.php.tpl",
                            array('%LIMB_PARENT_DIR%' => realpath(dirname(__FILE__) . '/../../../')));

    echo "done!";
  }

  function help($argv)
  {
    $txt = <<<EOD
Usage:
  web_app create <dst_dir> [<app_name>]

  Creates new WEB_APP based skeleton application at specified directory <dst_dir>.

EOD;
    echo $txt;
  }

  protected function _resolveTemplate($template, $vars = array())
  {
    $file = substr($template, 0, strrpos($template, '.'));
    file_put_contents($file, strtr(file_get_contents($template), $vars));
    unlink($template);
  }
}


