<?php

class lmbImageCreateFailedException extends lmbException 
{

  function __construct($file_name)
  {
  	parent::__construct('Image create is failed', array('file' => $file_name));
  }

}
?>