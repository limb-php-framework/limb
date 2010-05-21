<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliBaseCmd.class.php');
lmb_require('limb/cli/src/lmbCliException.class.php');
/**
 * class lmbCliRunner.
 *
 * @package cli
 * @version $Id$
 */
class lmbCliRunner
{
  protected $input;
  protected $output;
  protected $return_on_exit = false;

  function __construct(lmbCliInput $input, lmbCliOutputInterface $output)
  {
    $this->input = $input;
    $this->output = $output;
  }

  static function commandFileToClass($path)
  {
    $file_name = basename($path);
    return substr($file_name, 0, strpos($file_name, '.'));
  }

  function returnOnExit($flag = true)
  {
    $this->return_on_exit = $flag;
  }

  function execute($command_file)
  {
    if(!$command = $this->_mapCommandToObject($command_file))
      throw new lmbCliException(
        "Command '$command_file' is invalid(could not map it to the command class)", array('command' => $command_file)
      );
    try
    {

      $validate_result = $command->validate();
      if(false === $validate_result)
      {
        echo $command->help();
        return $this->_exit(1);
      }
      elseif(true === $validate_result)
      {
        $result = (int) $command->execute();
        return $this->_exit($result);
      }
      else
      {
        throw new lmbCliException(
          'validate() method of command object should return bool value', array('value' => $validate_result)
        );
      }
    }
    catch (lmbException $e)
    {
      $this->output->exception($e);
    }
  }

  protected function _exit($code = 0)
  {
    if($this->return_on_exit)
      return;
    else
      exit($code);
  }

  protected function _mapCommandToObject($command_file)
  {
    if(!file_exists($command_file))
    {
      $this->output->error("Command file '{$command_file}' not found");
      return $this->_exit(1);
    }
    if(!is_file($command_file))
    {
      $this->output->error("Given command file '{$command_file}' not a file");
      return $this->_exit(1);
    }
    $class = self :: commandFileToClass($command_file);
    $object = $this->_createCommandObject($command_file, $class);
    if(!$object instanceof lmbCliBaseCmd)
    {
      $this->_error("Created command not a instance of lmbCliBaseCmd", array('object' => $object));
      return $this->_exit(1);
    }
    return $object;
  }

  protected function _createCommandObject($command_file, $class)
  {
      require_once($command_file);
      return new $class($this->input, $this->output);
  }
}

