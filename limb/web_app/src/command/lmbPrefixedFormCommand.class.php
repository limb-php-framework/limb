<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/command/lmbFormCommand.class.php');

/**
 * class lmbPrefixedFormCommand.
 *
 * @package web_app
 * @version $Id: lmbPrefixedFormCommand.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbPrefixedFormCommand extends lmbFormCommand
{
  function getRequestData()
  {
    return $this->request->getArray($this->form_id);
  }

  function isSubmitted()
  {
    return !is_null($this->request->getPost($this->form_id));
  }

  protected function _validate()
  {
    $this->validator->setErrorList($this->error_list);
    $this->validator->validate(new lmbSet($this->getRequestData()));
  }
}
?>
