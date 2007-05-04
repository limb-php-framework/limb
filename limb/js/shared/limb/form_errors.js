/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_errors.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.form_errors');

Limb.form_errors.get_label_for_field = function (id)
{
  if(document.getElementsByTagName('label').length > 0)
  {
    labels = document.getElementsByTagName('label');
    for(c=0; c<labels.length; c++)
    {
      if(labels[c].htmlFor == id)
        return labels[c].innerHTML;
    }
  }
  return null;
}

Limb.form_errors.default_form_field_error_printer = function (id, msg)
{
  obj = document.getElementById(id);
  span = document.createElement('SPAN');
  br = document.createElement('BR');
  text = document.createTextNode(msg);
  span.appendChild(text);
  span.style.color = 'red';

  obj.parentNode.insertBefore(span, obj);
  obj.parentNode.insertBefore(br, obj);
  obj.style.borderColor = 'red';
  obj.style.borderStyle = 'solid';
  obj.style.borderWidth = '1px';
}

Limb.form_errors.default_form_field_error_label_printer = function (id, msg)
{
  var span = null;
  var i = 0;
  do
  {
    span = document.getElementById("label_for_" + id + "_" + i);
  }
  while(span && span.firstChild);

  if(!span) return;

  label = Limb.form_errors.get_label_for_field(id);

  //dirty workaround for non-labelled fields
  if(!label) label = id;

  newa = document.createElement('a');
  newa.appendChild(document.createTextNode(label));
  newa.href = '#'+id;
  newa.isid = id;
  newa.onclick = function()
  {
    try
    {
      Limb.require('Limb.tabs');
      if(tab = Limb.tabs.find_element_tab(this.isid))
        tab.activate();

      if(e = document.getElementById(this.isid))
        e.focus();
    }
    catch(ex){}

    return false;
  }

  var content = span.firstChild
  span.insertBefore(newa, content);
  if(content)
  {
    span.insertBefore(document.createTextNode(' ('), content);
    span.appendChild(document.createTextNode(')'), content);
  }
}

Limb.form_errors.set_form_field_error = function (id, msg)
{
  obj = document.getElementById(id);
  if(!obj) return;

  if(typeof(Limb.form_errors.form_field_error_printer) == "function")
    Limb.form_errors.form_field_error_printer(id, msg);
  else
    Limb.form_errors.default_form_field_error_printer(id, msg);

  if(typeof(Limb.form_errors.form_field_error_label_printer) == "function")
    Limb.form_errors.form_field_error_label_printer(id, msg);
  else
    Limb.form_errors.default_form_field_error_label_printer(id, msg);
}

Limb.form_errors.check_form_errors = function ()
{
  //someday client validation will be here
  return true;
}