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
    private \$__class_name;
    private \$__conn;
    private \$__lazy_attributes;
    private \$__exported;
    private \$__original;
    ";
  }

  function onConstructor()
  {
    return "
    \$this->__record = \$args[0];
    \$this->__class_name = \$args[1];
    \$this->__conn = \$args[2];
    \$this->__lazy_attributes = \$args[3];
    ";
  }

  function onMethod($method)
  {
    if($method == "get")
    {
      return "
      if(!\$this->__original)
      {
        if(\$this->__record->has(\$args[0]))
          return \$this->__record->get(\$args[0]);
        else
          \$this->__loadOriginal();
      }
      return \$this->__original->get(\$args[0]);
      ";
    }
    else if($method == "__get")
    {
      return "
      if(!\$this->__original)
      {
        if(\$this->__record->has(\$args[0]))
          return \$this->__record->get(\$args[0]);
        else
          \$this->__loadOriginal();
      }
      \$key = \$args[0];
      return \$this->__original->\$key;
      ";
    }
    else if($method == "getId")
    {
      return "
      if(!\$this->__original)
        return \$this->__record->get('id');
      return \$this->__original->getId();
      ";
    }
    else if($method == "__sleep")
    {
      return ";
    if(!\$this->__original)
      \$this->__loadOriginal();
    foreach(get_object_vars(\$this->__original) as \$k => \$v)
      \$this->\$k = \$v;
    return \$this->__original->__sleep();
    ";
    }
    else
    {
      return "
      if(!\$this->__original)
        \$this->__loadOriginal();
      return call_user_func_array(array(\$this->__original, '$method'), \$args);
      ";
    }
  }

  function onExtra()
  {
    return "
  private function __loadOriginal()
  {
    \$this->__original = new \$this->__class_name(null, \$this->__conn);
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
