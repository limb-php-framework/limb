<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileUploadMaxSizeRule.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbFileUploadMaxSizeRule extends lmbSingleFieldRule
{
  protected $max_size;

  function __construct($field_name, $max_size = NULL, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->max_size = $max_size;
  }

  function check($value)
  {
    if (!is_null($this->max_size) && ($value['size'] > (int)$this->max_size))
    {
      $this->error('{Field} - uploaded file was too large. Maximum size is {maxsize}.',
                   array('maxsize' => $this->_sizeToHuman($this->max_size)));
      return;
    }

    if ($value['error'] == UPLOAD_ERR_INI_SIZE ||
         $value['error'] == UPLOAD_ERR_FORM_SIZE)
    {
      $this->error('{Field} - file was too large to upload.'));
    }
  }

  protected function _sizeToHuman($filesize = 0)
  {
    if ($filesize < 1024)
      return $filesize . "B";

    if ($filesize >= 1024 && $filesize < 1048576)
      return sprintf("%.2fKB", $filesize / 1024);

    return sprintf("%.2fMB", $filesize / 1048576);
  }
}
?>