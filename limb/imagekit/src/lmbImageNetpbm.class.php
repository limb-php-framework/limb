<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbImageNetpbm.class.php 5651 2007-04-13 10:28:24Z pachanga $
 * @package    imagekit
 */
lmb_require('limb/imagekit/src/lmbImageLibrary.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

// Read utilities
define('JPEGTOPNM', 'jpegtopnm');
define('GIFTOPNM', 'giftopnm');
define('PNGTOPNM', 'pngtopnm');
define('BMPTOPNM', 'bmptopnm');
define('TIFFTOPNM', 'tifftopnm');

// Write utilities
define('PNMTOJPEG', 'pnmtojpeg');
define('PNMTOGIF', 'ppmtogif');
define('PNMTOPNG', 'pnmtopng');
define('PNMTOBMP', 'ppmtobmp');
define('PNMTOTIFF', 'pnmtotiff');

// Editor utilities
define('PNMSCALE', 'pnmscale');
define('PNMCUT', 'pamcut');
define('PNMFLIP', 'pnmflip');
define('PNMROTATE', 'pnmrotate');
define('PNMQUANT', 'ppmquant');
define('PNMMAKE', 'ppmmake');
define('PNMCOMP', 'pnmpaste');

if(!defined('NETPBM_LIB_DIR'))
{
  if(lmbSys :: osType() == 'win32')
    define('NETPBM_LIB_DIR', 'c:/netpbm/');
  else
    define('NETPBM_LIB_DIR', '/usr/local/netpbm/bin/');
 }

lmb_require('limb/imagekit/src/lmbImageLibrary.class.php');

class lmbImageNetpbm extends lmbImageLibrary
{
  var $lib_dir = '';
  var $os = '';
  var $ext = '';
  var $cmd_array = array();
  var $to_pnm = '';
  var $from_pnm = '';
  var $tmp_dir = '';
  var $current_input_file = '';
  var $current_input_file_type = '';
  var $current_output_file = '';
  var $current_output_file_type = '';

  function lmbImageNetpbm($lib_dir = NETPBM_LIB_DIR)
  {
    $this->lib_dir = $lib_dir;

    $this->_determineOptions();
  }

  function _determineOptions()
  {
    if(lmbSys :: osType() == "win32")
      $this->ext = '.exe';

    $this->_determineReadTypes();
    $this->_determineWriteTypes();

    if(sizeof($this->read_types) == 0)
      $this->library_installed = false;
    else
      $this->library_installed = true;
  }

  function _determineReadTypes()
  {
    if(file_exists($this->lib_dir . JPEGTOPNM . $this->ext))
      $this->read_types[] = 'JPEG';

    if(file_exists($this->lib_dir . GIFTOPNM . $this->ext))
      $this->read_types[] = 'GIF';

    if(file_exists($this->lib_dir . PNGTOPNM . $this->ext))
      $this->read_types[] = 'PNG';

    if(file_exists($this->lib_dir . BMPTOPNM . $this->ext))
      $this->read_types[] = 'BMP';

    if(file_exists($this->lib_dir . TIFFTOPNM . $this->ext))
      $this->read_types[] = 'TIFF';
  }

  function _determineWriteTypes()
  {
    if(file_exists($this->lib_dir . PNMTOJPEG . $this->ext))
      $this->create_types[] = 'JPEG';

    if(file_exists($this->lib_dir . PNMTOGIF . $this->ext))
      $this->create_types[] = 'GIF';

    if(file_exists($this->lib_dir . PNMTOPNG . $this->ext))
      $this->create_types[] = 'PNG';

    if(file_exists($this->lib_dir . PNMTOBMP . $this->ext))
      $this->create_types[] = 'BMP';

    if(file_exists($this->lib_dir . PNMTOTIFF . $this->ext))
      $this->create_types[] = 'TIFF';
  }

  function setInputFile($file_name, $type = '')
  {
    parent :: setInputFile($file_name, $type);

    $this->to_pnm = constant(strtoupper($type . 'TOPNM')) . " $file_name";
  }

  function setOutputFile($file_name, &$type)
  {
    parent :: setOutputFile($file_name, $type);

    $this->from_pnm = '';

    if(strtoupper($type) == 'GIF')
      $this->from_pnm = PNMQUANT . ' 256 | ';

      $this->from_pnm .= constant(strtoupper('PNMTO' . $type)) . " > $file_name";
  }

  function reset()
  {
    $this->cmd_array = array();
  }

  function commit()
  {
    if(!$this->library_installed)
      return false;

    array_unshift($this->cmd_array, $this->_toPnm());
    array_push($this->cmd_array, $this->from_pnm);

    echo $cmd = implode(' | ', $this->cmd_array);
    $this->_runCmd($cmd);

    $this->reset();

    if(is_file($this->current_output_file))
      unlink($this->current_output_file);

    $this->current_output_file = '';
    $this->current_output_file_type = '';
    $this->current_input_file = '';
    $this->current_input_file_type = '';

    return true;
  }

  function resize($params)
  {
    if(!$this->library_installed)
      return false;

    $info = getimagesize($this->input_file);
    $src_width = $info[0];
    $src_height = $info[1];

    list($dst_width, $dst_height) = $this->getDstDimensions($src_width, $src_height, $params);

    $this->cmd_array[] = PNMSCALE . " -width={$dst_width} -height={$dst_height}";
  }

  function rotate($angle, $bg_color = '')
  {
    if(!$this->library_installed)
      return false;

    $this->cmd_array[] = PNMROTATE . " {$angle}";
  }

  function flip($params)
  {
    if(!$this->library_installed)
      return false;

    if($params == FLIP_HORIZONTAL)
      $args = '-leftright';

    if($params == FLIP_VERTICAL)
      $args = '-topbottom';

    $this->cmd_array[] = PNMFLIP . " {$args}";
  }

  function _toPnm()
  {
    if(!empty($this->current_input_file))
    {
      $file_name = $this->current_input_file;
      $type = $this->current_input_file_type;
    }
    else
    {
      $file_name = $this->input_file;
      $type = $this->input_file_type;
    }

    return constant(strtoupper($type . 'TOPNM')) . " $file_name";
  }

  function _fromPnm()
  {
    if(!empty($this->current_output_file))
    {
      $file_name = $this->current_output_file;
      $type = $this->current_output_file_type;
    }
    else
    {
      $file_name = $this->output_file;
      $type = $this->output_file_type;
    }

    return constant(strtoupper('PNMTO' . $type)) . " > $file_name";
  }

  function _runCmd($cmd)
  {
    $cwd = getcwd();
    chdir($this->lib_dir);
    shell_exec($cmd);
    chdir($cwd);
  }

  function cut($x, $y, $w, $h, $bg_color = '')
  {
    if(!$this->library_installed)
      return false;

    $tmp_file = lmbFs :: generateTmpFile('netpbm');
    $this->current_output_file = $tmp_file;
    $this->current_output_file_type = 'BMP';

    if(sizeof($this->cmd_array) > 0)
    {
      array_unshift($this->cmd_array, $this->_toPnm());
      array_push($this->cmd_array, $this->_fromPnm());

      $cmd = implode(' | ', $this->cmd_array);

      $this->_runCmd($cmd);

      $file_name = $this->current_input_file = $tmp_file;
      $type = $this->current_input_file_type = $this->current_output_file_type;

      $this->reset();
    }
    else
    {
      $file_name = $this->input_file;
      $type = $this->input_file_type;
    }

    $info = getimagesize($file_name);

    if($x < 0)
    {
      $cx = 0;
      $cw = $w + $x;
    }
    else
    {
      $cx = $x;
      $cw = $w;
    }

    if($y < 0)
    {
      $cy = 0;
      $ch = $h + $y;
    }
    else
    {
      $cy = $y;
      $ch = $h;
    }

    if($cx + $cw > $info[0])
      $cw = $info[0] - $cx;

    if($cy + $ch > $info[1])
      $ch = $info[1] - $cy;

    $tmp_file = lmbFs :: generateTmpFile('netpbm');

    $cmd = $this->_toPnm() . ' | ' . PNMCUT . " $cx $cy $cw $ch > {$tmp_file}";

    $this->_runCmd($cmd);

    $cx = ($x < 0) ? -$x : 0;
    $cy = ($y < 0) ? -$y : 0;
    $cmd = PNMMAKE . " " . $this->_hexColorToX11($bg_color) . " {$w} {$h} | " . PNMCOMP . " {$tmp_file} {$cx} {$cy}" . ' | ' . $this->_fromPnm();

    $this->_runCmd($cmd);

    unlink($tmp_file);

    $this->current_input_file = $this->current_output_file;
    $this->current_input_file_type = $this->current_output_file_type;
  }
}
?>