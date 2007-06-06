<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * class lmbFileUploadMimeTypeRule.
 *
 * @package validation
 * @version $Id: lmbFileUploadMimeTypeRule.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
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