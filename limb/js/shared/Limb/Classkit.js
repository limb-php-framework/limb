/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: Classkit.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.Classkit');

Limb.require('Limb.Object');
Limb.require('Limb.Exception');

Limb.Classkit.createClass = function(body)
{
  var new_class = function()
  {
    if(Limb.is_function(this['parent']))
      this.parent = Limb.Classkit.makeParentProxy(this, this.parent);

    this.__construct.apply(this, arguments);
  }

  new_class.toString = Limb.Classkit.classToString;

  Limb.Classkit.inherit(new_class, Limb.Object, body);

  return new_class;
}

Limb.Classkit.createInterface = function(body)
{
  var new_interface = function()
  {
    throw new Limb.Exception('InterfaceException', 'Can not create instance of interface');
  }

  new_interface.toString = Limb.Classkit.interfaceToString;

  Limb.Classkit.extendInterface(new_interface, body || null);

  return new_interface;
}

Limb.Classkit.clone = function(target, source)
{
  for(var i in source)
  {
    if(Limb.is_object(source[i]) || i == 'NAME')
      continue;

    target[i] = source[i];
  }
}

Limb.Classkit.makeParentProxy = function(child_class, parent_class)
{
  var proxy = new Object();
  proxy['NAME'] = parent.NAME;

  var parent_instance = new parent_class();

  for(var method in parent_instance)
  {
    if(!Limb.is_function(parent_instance[method]))
      continue;

    proxy[method] = Limb.Classkit.parentMethodWrapper(child_class, parent_instance, method);
  }

  return proxy;
}

Limb.Classkit.inherit = function(child_class, parent_class, child_body)
{
  Limb.Classkit.clone(child_class, parent_class);
  Limb.Classkit.clone(child_class.prototype, parent_class.prototype);

  if(Limb.is_function(parent_class))
    child_class.prototype.parent = parent_class;

  if(Limb.isset(child_body))
    Limb.Classkit.extendClass(child_class, child_body);
}

Limb.Classkit.implement = function(class_ref, interface_ref, class_body)
{
  if(Limb.isset(class_body))
    Limb.Classkit.extendClass(class_ref, class_body);

  Limb.Classkit.checkInterfaceImplementation(class_ref, interface_ref);
}

Limb.Classkit.extendClass = function(class_ref, class_body)
{
  Limb.Classkit.clone(class_ref.prototype, class_body);

  if(Limb.is_object(class_body['static']))
    Limb.Classkit.clone(class_ref, class_body['static']);
}

Limb.Classkit.checkInterfaceImplementation = function(child_class, interface_ref)
{
  for(var method in interface_ref)
  {
    if(!Limb.is_function(interface_ref[method]))
      continue;

    if(!Limb.is_function(child_class.prototype[method]) && !Limb.is_function(child_class[method]))
      throw new Limb.Exception('InterfaceException', "Method '" + method + "' not implemented");
  }
}

Limb.Classkit.extendInterface = function(interface_ref, body)
{
  if(!Limb.is_object(body))
    return;

  for(var method in body)
  {
    if(!Limb.is_function(body[method]))
      continue;

    interface_ref[method] = Limb.Classkit.interfaceMethodPrototype;
  }
}

Limb.Classkit.parentMethodWrapper = function(child_class_ref, parent_class_ref, method)
{
  return function()
  {
    return parent_class_ref[method].apply(child_class_ref, arguments);
  }
}

Limb.Classkit.interfaceMethodPrototype = function()
{
  throw new Limb.Exception('InterfaceException', 'Interface methods can not be called');
}

Limb.Classkit.classToString = function()
{
  return '[ class ' + this.prototype.NAME + ' ]';
}

Limb.Classkit.interfaceToString = function()
{
  return '[ interface ' + this.NAME + ' ]';
}

Limb.Class = function(class_name, body)
{
  var class_ref = Limb.Classkit.createClass(body || null);
  class_ref.prototype.NAME = class_name;
  class_ref.prototype.static = class_ref;

  Limb.define(class_name, class_ref);

  return class_ref;
}

Limb.Interface = function(interface_name, body)
{
  var interface_ref = Limb.Classkit.createInterface(body || null);
  interface_ref.NAME = interface_name;
  interface_ref.is_interface = true;

  Limb.define(interface_name, interface_ref);

  return interface_ref;

}

