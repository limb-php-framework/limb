/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

if(Limb == undefined) var Limb = {};

Limb.get = function(id) {
  return document.getElementById(id);
}
 
Limb.trim = function(str)
{
  var r = /^\s+|\s+$/;
  return str.replace(r,'');
}

if(!String.prototype.trim)
{
  String.prototype.trim = Limb.trim;
}

if(!Function.prototype.bind)
{
  Function.prototype.bind = function( object, args )
  {
    var __method = this;
    var __args = args;
    return function()
    {
      __method.apply( object, __args || arguments );
    };
  };
}

if(!Function.prototype.bindAsEventListener)
{
  Function.prototype.bindAsEventListener = function(object)
  {
    var __method = this;
    return function(event) {__method.call(object, event || window.event);};
  };
}

Limb.Exception = function()
{
  if(arguments.length == 1 && Limb.isObject(arguments[0]))
  {
    this.type = arguments[0].type || 'LimbException';
    this.message = arguments[0].message;
    this.stack = arguments[0].stack || 'Stack is not available';
    this.file_name = arguments[0].fileName || 'File name is not available';
    this.line_number = arguments[0].lineNumber || 'Line number is not available';
  }
  else
  {
    this.type = arguments[0] || 'LimbException';
    this.message = arguments[1] || 'Unknown error';
    if(typeof(arguments[2]) == 'object')
    {
      this.stack = arguments[2].stack || e.stack;
      this.file_name = arguments[2].fileName || e.fileName;
      this.line_number = arguments[2].lineNumber || e.lineNumber;
    }
    else
    {
      this.stack = 'Stack is not available';
      this.file_name = 'File name is not available';
      this.line_number = 'Line number is not available';
    }
  }
}

Limb.Exception.prototype =
{
  getMessage: function()
  {
    return this.message;
  },

  getType: function()
  {
    return this.type;
  },

  getStack: function()
  {
    return this.stack;
  },

  getFileName: function()
  {
    return this.file_name;
  },

  getLineNumber: function()
  {
    return this.line_number;
  },

  toString: function()
  {
    return '[ exception ' + this.type + ' ]';
  }
}

Limb.define = function(name, value)
{
  var parts = name.split('.');
  var var_name = parts.pop();

  var namespace = Limb.namespace(parts.join('.'));
  namespace[var_name] = value;
}

Limb.namespace = function(name)
{
  var parts = name.split('.');
  var parent = window;
  for(var i=0; i<parts.length; i++)
  {
    if(!parts[i])
      continue;

    if(!parent[parts[i]])
      parent[parts[i]] = {};

    parent = parent[parts[i]];
  }

  return parent;
}

Limb.require = function(package_name)
{
  //no working function for now. It needs to be written from the scratch or may be ported from 2.x
}

Limb.isset = function(variable)
{
  return typeof(variable) != 'undefined' && variable != null;
}

Limb.isObject = function(variable)
{
  return typeof(variable) == 'object';
}

Limb.isFunction = function(variable)
{
  return typeof(variable) == 'function';
}

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example Limb.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example Limb.cookie('the_cookie', 'the_value', {expires: 7, path: '/', domain: 'jquery.com', secure: true});
 * @desc Create a cookie with all available options.
 * @example Limb.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example Limb.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value.
 */
Limb.cookie = function(name, value, options)
{
  if(typeof value != 'undefined') // name and value given, set cookie
  {
    options = options || {};
    if(value === null)
    {
      value = '';
      options.expires = -1;
    }
    var expires = '';
    if(options.expires && (typeof options.expires == 'number' || options.expires.toUTCString))
    {
        var date;
        if(typeof options.expires == 'number')
        {
          date = new Date();
          date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
        }
        else
          date = options.expires;
        expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
    }
    var path = options.path ? '; path=' + options.path : '';
    var domain = options.domain ? '; domain=' + options.domain : '';
    var secure = options.secure ? '; secure' : '';
    document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
  }
  else
  { // only name given, get cookie
    var cookieValue = null;
    if(document.cookie && document.cookie != '')
    {
      var cookies = document.cookie.split(';');
      for(var i = 0; i < cookies.length; i++)
      {
        var cookie = Limb.trim(cookies[i]);
        // Does this cookie string begin with the name we want?
        if(cookie.substring(0, name.length + 1) == (name + '='))
        {
          cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
          break;
        }
      }
    }
    return cookieValue;
  }
}

Limb.namespace('Limb.Classkit');

Limb.Classkit.createClass = function(body)
{
  var new_class = function()
  {
    if(Limb.isFunction(this['parent']))
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
    if(Limb.isObject(source[i]) || i == 'NAME')
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
    if(!Limb.isFunction(parent_instance[method]))
      continue;

    proxy[method] = Limb.Classkit.parentMethodWrapper(child_class, parent_instance, method);
  }

  return proxy;
}

Limb.Classkit.inherit = function(child_class, parent_class, child_body)
{
  Limb.Classkit.clone(child_class, parent_class);
  Limb.Classkit.clone(child_class.prototype, parent_class.prototype);

  if(Limb.isFunction(parent_class))
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

  if(Limb.isObject(class_body['static']))
    Limb.Classkit.clone(class_ref, class_body['static']);
}

Limb.Classkit.checkInterfaceImplementation = function(child_class, interface_ref)
{
  for(var method in interface_ref)
  {
    if(!Limb.isFunction(interface_ref[method]))
      continue;

    if(!Limb.isFunction(child_class.prototype[method]) && !Limb.isFunction(child_class[method]))
      throw new Limb.Exception('InterfaceException', "Method '" + method + "' not implemented");
  }
}

Limb.Classkit.extendInterface = function(interface_ref, body)
{
  if(!Limb.isObject(body))
    return;

  for(var method in body)
  {
    if(!Limb.isFunction(body[method]))
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

Limb.namespace('Limb.Object');

Limb.Object.inherits = function(class_name, body)
{
  var parent_class = Limb.namespace(class_name);
  if(!parent_class)
    return this;

  Limb.Classkit.inherit(this, parent_class, body);

  return this;
}

Limb.Object.implements = function(interface_name, body)
{
  var interface_ref = Limb.namespace(interface_name);
  if(!interface_ref)
    return this;

  Limb.Classkit.implement(this, interface_ref, body);

  return this;
}

Limb.Object.prototype = {
  __construct: function() {},

  toString: function()
  {
    return '[ object ' + this.NAME + ' ]';
  }
}

Limb.namespace('Limb.Browser');

var agt = navigator.userAgent.toLowerCase();
Limb.Browser.is_ie = (agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1);
Limb.Browser.is_gecko = navigator.product == "Gecko";
Limb.Browser.is_opera  = (agt.indexOf("opera") != -1);
Limb.Browser.is_mac    = (agt.indexOf("mac") != -1);
Limb.Browser.is_mac_ie = (Limb.Browser.is_ie && Limb.Browser.is_mac);
Limb.Browser.is_win_ie = (Limb.Browser.is_ie && !Limb.Browser.is_mac);

