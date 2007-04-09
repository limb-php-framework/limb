/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: Limb.js 5568 2007-04-09 08:17:15Z wiliam $
 * @package    js
 */

if(Limb == undefined) var Limb = {};

String.prototype.trim = function()
{
  var r=/^\s+|\s+$/;
  return this.replace(r,'');
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

Limb.is_object = function(variable)
{
  return typeof(variable) == 'object';
}

Limb.is_function = function(variable)
{
  return typeof(variable) == 'function';
}
