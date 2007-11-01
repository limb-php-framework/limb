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


