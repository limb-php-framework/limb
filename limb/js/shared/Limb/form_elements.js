/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form_elements.js 5436 2007-03-30 07:30:57Z tony $
 * @package    js
 */

Limb.namespace('Limb.DynamicList');

Limb.DynamicList = function(button_id, answer_sample_id, container_id, init_arr, init_func)
{
  this.create_button = document.getElementById(button_id)
  this.answer_sample = document.getElementById(answer_sample_id)
  this.container = document.getElementById(container_id)
  this.create_button.controller = this
  this.init_arr = init_arr
  this.init_func = init_func
  this.index = 0
  this.init()
}

Limb.DynamicList.is_modified = 0
Limb.DynamicList.set_modified_flag = function()
{
  Limb.DynamicList.is_modified = 1
}
Limb.DynamicList.prototype.init = function()
{
  for(i=0; i<this.init_arr.length; i++)
  {
    this.add_item();
  }

  this.create_button.onclick = function()
  {
    this.controller.add_item()
    Limb.DynamicList.set_modified_flag()
    return false
  }
  this.answer_sample.style.display = 'none'
}

Limb.DynamicList.prototype.add_item = function(top_obj)
{
  var div = document.createElement('div');
  if(top_obj)
  {
    var next = top_obj.nextSibling
    if(next)
      div = this.container.insertBefore(div, next)
    else
      this.container.appendChild(div)
  }
  else
  {
    this.container.appendChild(div)
  }
  div.innerHTML = this.answer_sample.innerHTML

  this.init_func(div, this.index, this.init_arr)

  this.init_behavior(div)
  this.index++
}
Limb.DynamicList.prototype.init_behavior = function(div)
{
  div.name = 'Limb.DynamicList_row'

  var del = Limb.DynamicList.get_obj_by_id(div, 'del');
  del.id = del.id + this.index;
  var add = Limb.DynamicList.get_obj_by_id(div, 'add')
  add.id = add.id + this.index;
  var up = Limb.DynamicList.get_obj_by_id(div, 'up')
  up.id = up.id + this.index;
  var down = Limb.DynamicList.get_obj_by_id(div, 'down')
  down.id = down.id + this.index;

  del.controller =
  add.controller =
  up.controller =
  down.controller = this;

  up.div =
  down.div =
  add.div = div

  del.onclick =  function()
  {
    var node = this
    while((node = node.parentNode) != null)
    {

      if(node)
        if(node.name == 'Limb.DynamicList_row')
        {
          node.parentNode.removeChild(node)
          Limb.DynamicList.set_modified_flag()
        }
    }
  }
  add.onclick =  function()
  {
    this.controller.add_item(this.div)
    Limb.DynamicList.set_modified_flag()
  }
  up.onclick =  function()
  {
    if(!this.div.previousSibling) return
    var tmp = this.div.parentNode.removeChild(this.div.previousSibling)

    if(this.div.nextSibling)
      this.controller.container.insertBefore(tmp, this.div.nextSibling)
    else
      this.controller.container.appendChild(tmp)

    Limb.DynamicList.set_modified_flag()
  }
  down.onclick =  function()
  {
    if(!this.div.nextSibling) return
    var tmp = this.div.parentNode.removeChild(this.div.nextSibling)

    this.controller.container.insertBefore(tmp, this.div)

    Limb.DynamicList.set_modified_flag()
  }
}
Limb.DynamicList.prototype.fix_select_values = function(original, clone)
{
  var selects = original.getElementsByTagName('select')
  var clone_selects = clone.getElementsByTagName('select')

  for(var i = 0; (i < clone_selects.length); i++)
    clone_selects[i].selectedIndex = selects[i].selectedIndex;
}

Limb.DynamicList.get_obj_by_id = function(node, id)
{
  if(node.id == id)
    return node;

  if(!node.hasChildNodes())
    return null;

  result = null;

  for(var i = 0; (i < node.childNodes.length && !result); i++)
    result = Limb.DynamicList.get_obj_by_id(node.childNodes[i], id);

  return result;
}

Limb.namespace('Limb.DoubleSelect');

Limb.DoubleSelect = Class.create();

Limb.DoubleSelect.prototype =
{
  initialize: function(instanceName)
  {
    // Properties
    this.instanceName	= instanceName;
    this.select = null;
    this.srcSelect = null;
    this.dstSelect = null;
    this.replaceSelect();
  },

  replaceSelect: function()
  {
    this.select = document.getElementById(this.instanceName);
    this.drawControl();
    this.drawOptions();
  },

  drawOptions: function()
  {
    this.dstSelect.options.length = 0;
    this.srcSelect.options.length = 0;

    for(i = 0; i < this.select.options.length; i++)
    {
      if(this.select.options[i].selected)
        option = this.addElement('option', this.dstSelect);
      else
        option = this.addElement('option', this.srcSelect);

      option.value = this.select.options[i].value;
      option.text = this.select.options[i].text;
      option.selected = false;
    }
  },

  drawControl: function()
  {
    this.select.style.display = 'none';
    var parent = this.select.parentNode;
    var div = this.addElement('div', parent);

    div.innerHTML = "<table><tr><td></td><td align='center' valign='middle'></td><td></td></tr></table>";
    container = div.childNodes[0].childNodes[0].childNodes[0];
    this.srcSelect = this.addSelector(container.childNodes[0]);
    this.makeupSelector(this.srcSelect, this.select)
    this.addButtons(container.childNodes[1]);
    this.dstSelect = this.addSelector(container.childNodes[2])
    this.makeupSelector(this.dstSelect, this.select)
  },

  makeupSelector: function(src, example)
  {
    if(example.style.width)
      src.style.width = example.style.width;
    else
      src.style.width = '200px';
    if(example.size)
      src.size = example.size;
    else if(example.style.height)
      src.style.height = example.style.height;
    else
      src.style.height = '200px';


  },

  addSelector: function(parent)
  {
    parent.innerHTML = "<select multiple></select>"
    return parent.firstChild;
  },

  addButtons: function(parent)
  {
    button = this.addElement('input', parent);
    button.type = 'button';
    button.value = '>>';
    button.style.display = 'inline';
    button.onclick = this.selectAll;
    button.selector_obj = this;

    this.addElement('br', parent);
    this.addElement('br', parent);
    button = this.addElement('input', parent);
    button.type = 'button';
    button.value = ' > ';
    button.style.display = 'inline';
    button.onclick = this.selectItems;
    button.selector_obj = this;

    this.addElement('br', parent);
    this.addElement('br', parent);
    button = this.addElement('input', parent);
    button.type = 'button';
    button.value = ' < ';
    button.style.display = 'inline';
    button.onclick = this.deselectItems;
    button.selector_obj = this;

    this.addElement('br', parent);
    this.addElement('br', parent);
    button = this.addElement('input', parent);
    button.type = 'button';
    button.value = '<<';
    button.style.display = 'inline';
    button.onclick = this.deselectAll;
    button.selector_obj = this;
  },

  addElement: function(type, parent)
  {
    element = document.createElement(type);
    parent.appendChild(element);
    return element;
  },

  selectAll: function()
  {
    var selector = this.selector_obj;
    for(i = 0; i < selector.select.options.length; i++)
      selector.select.options[i].selected = true;
    selector.drawOptions();
    return false;
  },

  deselectAll: function()
  {
    var selector = this.selector_obj;
    for(i = 0; i < selector.select.options.length; i++)
      selector.select.options[i].selected = false;
    selector.drawOptions();
    return false;
  },

  selectItems: function()
  {
    this.selector_obj.setSelection(this.selector_obj.srcSelect, this.selector_obj.select, true);
    this.selector_obj.drawOptions();
    return false;
  },

  deselectItems: function()
  {
    this.selector_obj.setSelection(this.selector_obj.dstSelect, this.selector_obj.select, false);
    this.selector_obj.drawOptions();
    return false;
  },

  setSelection: function(source, main, selected)
  {
    for(i = 0; i < source.options.length; i++)
    {
      if(!source.options[i].selected)
        continue;
      for(j = 0; j < main.options.length; j++)
      {
        if(main.options[j].value == source.options[i].value &&
           main.options[j].text == source.options[i].text)
        {
          main.options[j].selected = selected;
          continue;
        }
      }
    }
  }
}

