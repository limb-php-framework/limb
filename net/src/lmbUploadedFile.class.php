<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');

/**
 * class lmbUploadedFile.
 *
 * @package net
 * @version $Id$
 */
class lmbUploadedFile extends lmbObject
{
  function getFilePath()
  {
    return $this->getTmpName();
  }

  function getMimeType()
  {
    return $this->getType();
  }

  function move($dest)
  {
    return move_uploaded_file($this->getTmpName(), $dest);
  }

  function isUploaded()
  {
    return is_uploaded_file($this->getTmpName());
  }

  function isValid()
  {
    return $this->getError() == UPLOAD_ERR_OK;
  }

  function getContents()
  {
    return file_get_contents($this->getTmpName());
  }

  function destroy()
  {
    unlink($this->getTmpName());
  }
}


