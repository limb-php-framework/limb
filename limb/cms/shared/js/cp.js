var LIMB_WINDOW_DEFAULT_PARAMS = { width: 890, height:500, resizable: true, noautoresize: true };

function MarkAll(mark)
{
  var objects = document.getElementsByName('ids[]');

  if(objects != null)
  {
    for(i = 0; i < objects.length; i++)
      objects[i].checked = mark;
  }
}

function control_filter()
{
  if(jQuery(this).next().attr('class')!='filter')
    return;

  if(jQuery('.header_context .filter').css('display')=='none')
  {
    jQuery('.header_context .filter').slideDown('fast');
    Limb.cookie('filter', 1);
  }
  else
  {
    jQuery('.header_context form.filter').slideUp('fast');
    Limb.cookie('filter', 0);
  }
}

function control_error()
{
  if(jQuery('.message_error .content ol').css('display')=='none')
    jQuery('.message_error .content ol').slideDown('fast');
  else
    jQuery('.message_error .content ol').slideUp('fast');
}


Limb.namespace('CMS.forms');

CMS.forms.upload_file = function(uri, field_id, on_complete)
{
  var input = document.getElementById(field_id);
  if(!input || !input.value)
    throw "Файл является обязательным полем";

  var parent = input.parentNode;

  var iframe_id = field_id + '_worker_frame';
  if(Limb.Browser.is_ie)
    var iframe = document.createElement('<iframe id="' + iframe_id + '" name="' + iframe_id + '" />');
  else
  {
    var iframe = document.createElement('iframe');
    iframe.id = iframe_id;
    iframe.name = iframe_id;
  }
  iframe.src = '';

  var form = document.createElement('form');
  form.action = uri;
  form.method = 'post';
  form.target = iframe_id;
  form.style.display = 'none';

  if(form.encoding)
    form.encoding = 'multipart/form-data';
  else
    form.enctype = 'multipart/form-data';

  var hidden = document.createElement('input');
  hidden.type = 'hidden';
  hidden.name = 'UPLOAD_IDENTIFIER';
  hidden.value = field_id;

  parent.removeChild(input);
  parent.appendChild(form);
  form.appendChild(iframe);
  form.appendChild(hidden);
  form.appendChild(input);

  var new_file = document.createElement('input');
  new_file.type = 'hidden';
  new_file.name = 'video_id';

  function callback()
  {
    setTimeout(function () {
      var error = iframe.contentWindow.file_error;
      var file_name = iframe.contentWindow.file_name;
      if(!file_name)
        error = 'При загрузке файла произошла ошибка';
      var file_size = iframe.contentWindow.file_size;
      new_file.value = file_name;
      parent.appendChild(new_file);
      parent.removeChild(form);
      on_complete(file_name, file_size, error);
    }, 100);
  }

  if(window.attachEvent)
    iframe.attachEvent('onload', callback);
  else
    iframe.addEventListener('load', callback, false);

  form.submit();
}

CMS.forms.clear_image = function(form, field_id, hidden_id)
{
  hidden_id = hidden_id ? hidden_id : field_id + '_id';
  var hidden = document.getElementById(hidden_id);
  if(!hidden)
  {
    hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.id = hidden_id;
    hidden.name = hidden_id;
    form.appendChild(hidden);
  }

  hidden.value = '';

  var img = document.getElementById(field_id + '_preview');
  if(!img)
    return;

  img.parentNode.removeChild(img);
}

Limb.Class('CMS.UploadProgress',
{
  __construct: function(url, options)
  {
    this.options = options;
    this.onProgressStart = this.options.onProgressStart;
    this.onProgressComplete = this.options.onProgressComplete;
    this.onProgressChange = this.options.onProgressChange;
    this.onFailure = this.options.onFailure;

    this.frequency = this.options.frequency || 1000;

    this.request = {};
    this.url = url;
    this.error = 0;
    this.in_progress = false;

    this.start();
  },

  start: function()
  {
    this.in_progress = true;
    this.options.onProgressComplete = this.updateComplete.bind(this);
    (this.onProgressStart || function(){}).apply(this);
    this.onTimerEvent();
  },

  stop: function()
  {
    this.request.options.onProgressComplete = undefined;
    this.request.options.onFailure = undefined;
    clearTimeout(this.timer);
    this.onTimerEvent();
    this.in_progress = false;
  },

  updateComplete: function(request)
  {
    if(!this.in_progress)
    {
      if(this.error == 1)
        (this.onFailure || function(){}).apply(this, arguments);
      else
        (this.onProgressComplete || function(){}).apply(this, arguments);
      return;
    }

    if(request.responseText == 'stop')
    {
      this.error = 1;
      this.stop();
      return;
    }

    if(request.responseText == 'complete')
    {
      this.stop();
      return;
    }

    if(request.responseText != 'undefined')
    {
      (this.onProgressChange || function(){}).apply(this, [request.responseText]);
    }

    this.timer = setTimeout(this.onTimerEvent.bind(this), this.frequency);
  },

  onTimerEvent: function()
  {
    this.request = jQuery.ajax({
                               url: this.url,
                               error: this.onFailure,
                               success: this.updateComplete
    });
  }
});

function changed_field_highlighter()
{
  jQuery(this).change(function(){jQuery(this).prev('label').css({color: 'green'})});
}

jQuery(window).ready(function(){

  //filter up/down sliding control
  jQuery('.header_context .active_filter').bind('click', control_filter);
  jQuery('.message_error .show_hidden').bind('click', control_error);

  //cookied filter
  if(Limb.cookie('filter') == 1)
    jQuery('.header_context .filter').show();

  //resized images

  jQuery('img[@resize]')
    .one('load', function()
    {
      var current_img = jQuery(this);
      var size = current_img.attr('resize').replace('x','');
      var width_bool = false;
      var height_bool = false;
      var width = current_img.width();
      var height = current_img.height();

      current_img.css({position:'absolute',top:'3000'});

      if (current_img.attr('resize').indexOf('x') == 0)
          height_bool = true;
      else if (current_img.attr('resize').indexOf('x') == current_img.attr('resize').length - 1)
        width_bool = true;
      else if (current_img.attr('resize').indexOf('x') < 0)
          if (width > height)
            height_bool = true;
          else
            width_bool = true;

      var link = jQuery('<a class="magnifier" href="' + this.src + '" target="_blank"><img src="/images/_cp/icon/magnifier.gif" width="13" height="13" alt="magnifier"/></a>').get()[0];


      if(height_bool)
      {
        current_img.height(size);
      }
      else if (width_bool)
      {
        current_img.width(size);
      }

      jQuery(link).css('top', current_img.height() - 13);
      jQuery(link).css('left', current_img.width() - 16);

      current_img.css({position : 'static', top : '0'});
      current_img.before(link);

    });

    //duplicating h1 popup in title
    jQuery('.popup h1').each(
      function()
      {
        document.title = jQuery(this).text();
      }
    );

  //left navigation current item highlight
  var url = window.location.toString();
  var max = 0;
  var link = null;
  jQuery("#main_menu > dd > ul > li > a").each(function()
  {
    //finding the longest href
    if(url.indexOf(this.href) >= 0 && this.href.length > max)
    {
      link = this;
      max = this.href.length;
    }
  });

  if(link)
    jQuery(link).parent().attr('class', 'current');

  //sliding navigation support
  if(Limb.isFunction(jQuery.fn.accordion_cp))
    jQuery('#main_menu').accordion_cp();

  //changed form items highlight
  jQuery('input:text').each(changed_field_highlighter);
  jQuery('select').each(changed_field_highlighter);
  jQuery('textarea').each(changed_field_highlighter);
});

Limb.namespace('rt.Util');
rt.Util.clickAjax = function(link)
{
  jQuery.ajax({type: 'GET', url: link.href, success: function(){window.location.reload();}});
  return false;
}
