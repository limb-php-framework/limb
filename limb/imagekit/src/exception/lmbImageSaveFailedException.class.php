<?php

class lmbImageSaveFailedException extends lmbException 
{

  function __construct($file_name)
  {
  	parent::__construct('Image save is failed', array('file' => $file_name));
  }

}
?>