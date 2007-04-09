<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileUploadMimeTypeRule.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbFileUploadMimeTypeRule extends lmbSingleFieldRule
{
  protected $mime_types = array();

  function __construct($field_name, $mime_types = array(), $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->mime_types = $mime_types;
  }

  function check($value)
  {
    if (! empty($value['type']) &&
        ! in_array($value['type'], $this->mime_types))
    {
      $this->error('{Field} - uploaded file must be of type: {mime_types}.',
                   $values = array(),
                   $i18n_params = array('mime_types' => implode(', ', $this->mime_types)));
    }
  }
}
?>