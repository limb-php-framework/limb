<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileUploadMimeTypeRule.class.php 5413 2007-03-29 10:08:00Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbFileUploadMimeTypeRule extends lmbSingleFieldRule
{
  protected $mime_types = array();

  function __construct($field_name, $mime_types = array())
  {
    parent :: __construct($field_name);

    $this->mime_types = $mime_types;
  }

  function check($value)
  {
    if (! empty($value['type']) &&
        ! in_array($value['type'], $this->mime_types))
    {
      $this->error(lmb_i18n('{Field} - uploaded file must be of type: {mime_types}.',
                   array('mime_types' => implode(', ', $this->mime_types)), 'validation'));
    }
  }
}
?>