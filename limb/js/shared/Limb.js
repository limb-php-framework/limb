/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: Limb.js 5444 2007-03-30 11:28:01Z tony $
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
  document.write('<script type="text/javascript" src="' + package_name + '"></script>');
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
