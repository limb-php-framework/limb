/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

Limb.namespace('Limb.Form');

Limb.Form.changeAction = function (form, action)
{
  if(!form) return;
  form.action = action;
}

Limb.Form.addActionParameter = function (form, parameter, val)
{
  if(!form) return;
  form.action = Limb.Http.addUrlQueryItem(form.action + '', parameter, val);
}

Limb.Form.addHidden = function (form, parameter, val)
{
  if(!form) return;

  hidden = document.getElementById(parameter + '_hidden_parameter');
  if(hidden)
  {
    hidden.value = val;
    form.appendChild(hidden);
  }
  else
  {
    hidden = document.createElement('INPUT');
    hidden.id = parameter + '_hidden_parameter';
    hidden.type = 'hidden';
    hidden.name = parameter;
    hidden.value = val;
    form.appendChild(hidden);
  }
}

Limb.Form.submit = function (form, form_action)
{
  var iframe_id = form.id + '_worker_frame';

  if(Limb.Browser.is_ie)
    var iframe = document.createElement('<iframe id="' + iframe_id + '" name="' + iframe_id + '" />');
  else
  {
    var iframe = document.createElement('iframe');
    iframe.id = iframe_id;
    iframe.name = iframe_id;
  }
  iframe.src = '';
  iframe.style.display = 'none';

  form.appendChild(iframe);
  form.target = iframe_id;

  jQuery(iframe).bind('load', function(){
    window.location.reload();
  });

  if(form_action)
    form.action = form_action;

  form.submit();
}

Limb.Form.submitPopup = function (form, form_action)
{
  var w = new Limb.Window('', form.id + 'popup');
  form.target = w.getWindowObject().name;

  if(form_action)
    form.action = form_action;

  form.submit();

  w.onOpen();
}

Limb.Form.getFieldLabel = function (id)
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

Limb.Form.defaultFieldErrorPainter = function (id, msg)
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

Limb.Form.defaultFieldErrorLabelPainter = function (id, msg)
{
  var span = null;
  var i = 0;
  do
  {
    span = document.getElementById("label_for_" + id + "_" + i);
  }
  while(span && span.firstChild);

  if(!span) return;

  label = Limb.Form.getFieldLabel(id);

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

Limb.Form.setFieldError = function (id, msg)
{
  obj = document.getElementById(id);
  if(!obj) return;

  if(typeof(Limb.Form.fieldErrorPainter) == "function")
    Limb.Form.fieldErrorPainter(id, msg);
  else
    Limb.Form.defaultFieldErrorPainter(id, msg);

  if(typeof(Limb.Form.fieldErrorLabelPainter) == "function")
    Limb.Form.fieldErrorLabelPainter(id, msg);
  else
    Limb.Form.defaultFieldErrorLabelPainter(id, msg);
}
