<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/acl/src/lmbAcl.class.php');

class lmbAclConfigurator
{
  protected $acl;
  
  function __construct($options)
  {
    $default_inherits_policy = isset($options['default_inherits_policy']) ? $options['default_inherits_policy'] : true;
    $default_allow_policy =  isset($options['default_allow_policy']) ? $options['default_allow_policy'] : false;
    $this->acl = new lmbAcl($default_inherits_policy, $default_allow_policy);
    $this->add('addRole', isset($options['roles']) ? $options['roles'] : array());
    $this->add('addResource', isset($options['resources']) ? $options['resources'] : array());
    $this->addRules('allow', isset($options['allow']) ? $options['allow'] : array());
    $this->addRules('deny', isset($options['deny']) ? $options['deny'] : array());
  }
  
  function getAcl()
  {
    return $this->acl;
  }
  
  protected function add($method, $options)
  {
    foreach($options as $option => $value)
    {
      if (is_null($value))
        $this->acl->$method($option);
	  else
	    $this->acl->$method($option, (array) $value);
    }
  }
  
  protected function addRules($method, $options)
  {
    foreach($options as $role => $resources)
    {
      if (is_array($resources) and count($resources) > 0)
      {
        foreach($resources as $resource => $privileges)
        {
          if ($resource == '*')
            $resource = null;
            
          if (is_array($privileges) and !empty($privileges))
          {
            $this->acl->$method($role, $resource, $privileges);
          }
          else
            $this->acl->$method($role, $resource);
        }
      }
      elseif (!empty($resources))
      {
        $this->acl->$method($role, $resources);
      }
      else
        $this->acl->$method($role);
    }
  }
}