<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id:$
 * @package    web_app
 */
lmb_require('limb/util/src/system/lmbFs.class.php');

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

    echo "Copying skeleton application to '$dst_dir'...";
    lmbFs :: cp(dirname(__FILE__) . '/../skel', $dst_dir, '~^\.svn~');
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
}

?>