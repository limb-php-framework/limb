<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
abstract class lmbAbstractResourceProvider
{
  protected $_resource = null;

  function getResource()
  {
    if(is_null($this->_resource))
      throw new lmbAclException('Resource provider must have filled _resource property');
    return $this->_resource;
  }
}
