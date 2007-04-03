/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: Object.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.Object');

Limb.require('Limb.Classkit');

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
