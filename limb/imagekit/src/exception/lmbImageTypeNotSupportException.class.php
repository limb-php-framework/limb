<?php

class lmbImageTypeNotSupportException extends lmbException 
{

  function __construct($type = '')
  {
  	parent::__construct('Image type is not support', $type ? array('type' => $type) : array());
  }

}
?>