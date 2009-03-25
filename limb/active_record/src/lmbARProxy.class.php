<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/core/src/lmbDecorator.class.php');

class lmbARProxyGeneratorEventsHandler
{
  function onDeclareProperties()
  {
    return "
    private \$__record;
    private \$__default_class_name;
    private \$__conn;
    private \$__lazy_attributes;
    private \$__exported;
    ";
  }

  function onConstructor()
  {
    return "
    \$this->__record = \$args[0];
    \$this->__default_class_name = \$args[1];
    \$this->__conn = \$args[2];
    \$this->__lazy_attributes = \$args[3];
    \$this->__exported = false;
    ";
  }

  function onMethod($method)
  {
    if($method == "get")
    {
      return "\$this->__record->get(\$args[0]);";
    }
    else if($method == "__get")
    {
      return "
    \$name = \$args[0];
    \$just_exported = false;
    if(!\$this->__exported)
    {
      foreach(\$this->__record as \$key => \$val)
        \$this->\$key = \$val;
      \$just_exported = true;
      \$this->__exported = false;
    }

    if(\$just_exported && isset(\$this->\$name))
      return \$this->\$name;

    if(!\$this->__original)
      \$this->_loadOriginal();

    return \$this->__original->\$name;";

    }
    else
    {
      return 
      "if(!\$this->__original)
        \$this->__loadOriginal();
      return call_user_func_array(array(\$this->__original, '$method'), \$args);";
    }
  }

  function onExtra()
  {
    return "
  private function __loadOriginal()
  {
    if(\$path = \$this->__record->get(lmbActiveRecord :: getInheritanceField()))
    {
      \$class_name = lmbActiveRecord :: getInheritanceClass(\$this->__record);

      if(!class_exists(\$class_name))
        throw new lmbException(\"Class '\$class_name' not found\");
    }
    else
      \$class_name = \$this->__default_class_name;

    \$this->__original = new \$class_name(null, \$this->__conn);
    if(is_array(\$this->__lazy_attributes))
      \$this->__original->setLazyAttributes(\$this->__lazy_attributes);

    \$this->__original->loadFromRecord(\$this->__record);
  }
  ";
  }
}

/**
 * class lmbARProxy.
 *
 * @package active_record
 * @version $Id$
 */
class lmbARProxy
{
  static function generate($proxy_class, $proxied_class)
  {
    return lmbDecorator::generate($proxied_class, $proxy_class, new lmbARProxyGeneratorEventsHandler());
  }
}
