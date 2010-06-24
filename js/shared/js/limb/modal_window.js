jQuery.fn.showSpinner = function() {
   jQuery(this).addClass('spinner');
}

jQuery.fn.hideSpinner = function() {
  jQuery(this).removeClass('spinner');
}

Limb.Class('ModalWindow',
{
  __construct: function(container)
  {
    if (jQuery(container)[0])
      this.container = jQuery(container);
    else
      this.container = jQuery('body');
  },
  _build: function()
  {
    this._buildOverlay();

    this.window = jQuery('.modal_window');
    if (!this.window.is('div'))
    {
      jQuery('body').append('<div class="modal_window"></div>');
      this.window = jQuery('.modal_window');
      jQuery(document).bind('keydown',{that:this}, this.keyPressed);
    }
    else
      this.window.empty().show().css('background-image','url(../images/img/window_loader.gif)');
  },
  _buildOverlay: function()
  {
    this.windowOverlay = jQuery('.modal_window_overlay');
    if (!this.windowOverlay.is('div'))
    {
      this.container.append('<div class="modal_window_overlay"></div>');
      this.windowOverlay = jQuery('.modal_window_overlay');
    }
    else
      this.windowOverlay.show();
  },

  loadByUrl: function (url, addClass)
  {
    if(!url)
      return;
    var that = this;
    this._build();
    if (addClass)
    {
      this.addClass = addClass;
      this.window.addClass(this.addClass);
    }
    this.window.load(url,
      function()
      {
        that.window.prepend("<a href='#' class='close_icon' title='Закрыть'><img src='images/1x1.gif' alt='Закрыть'/></a>").css('background-image','none');
        that.window.find('.hide_modal_window, .close_icon').click(function(){that.hide();return false;});
        that.window.find('.hide_modal_window, #close_button').click(function(){that.hide();return false;});
      });
    return false;
  },
  loadById: function (target, addClass)
  {
    if(!target)
      return;
    this.target = jQuery(target);
    this.prevTarget = this.target.prev();
    if (!this.prevTarget[0])
      this.parentTarget = this.target.parent();
    this._build();
    var that = this;
    if (addClass)
    {
      this.addClass = addClass;
      this.window.addClass(this.addClass);
    }
    this.target.remove().appendTo(this.window).show();
    this.window.prepend("<a href='#' class='close_icon' title='Закрыть'><img src='images/1x1.gif' alt='Закрыть'/></a>").css('background-image','none').find('.hideModalWindow, .close_icon').click(function(){that.hideById();return false;});;
  },

  messageBlock: function (msg, parentNode)
  {
    parentNode = parentNode || this.container;
    var htmlMessage = "<a href='javascript:void(0)' class='close_icon' onclick='jQuery(this.parentNode).remove();' title='Закрыть'><img src='images/1x1.gif' alt='Закрыть' /></a>";
    htmlMessage += "<a href='javascript:void(0)' class='button'>OK</a>";

    jQuery(parentNode).append("<div class='message_block'><div class='msg'>" + msg + "</div>"+ htmlMessage + "</div>");

    var messageBlock = jQuery('.message_block');
    messageBlock.find('.button').eq(0).click(function(){jQuery(this.parentNode).remove();return false;});
    messageBlock.css('margin-top', (-1)*messageBlock.height()/2);
  },

  confirmBlock: function(msg, target)
  {
    var html = "<div class='confirm_block'>";
    html += "<a href='javascript:void(0)' class='close_icon' title='Закрыть' onclick='jQuery(this.parentNode).remove();jQuery(\".modalWindowOverlay\").hide();'><img src='images/1x1.gif' alt='Закрыть' /></a>";

    if (!target)
      var link = "javascript:void(0);";
    else
      var link = target.href;

    html += "<div class='msg'>" + msg + "</div>";
    html += "<a href='" + link + "' class='button' id='ok_button'>OK</a>";
    html += "<a href='#' class='button' id='cancel_button'>Отмена</a>";
    html += "</div>";

    this._buildOverlay();

    var elem = jQuery(html).appendTo(this.container);

    elem.css('margin-top', (-1)*elem.height()/2);

    elem.find('.button').eq(0).click(function(){jQuery(this.parentNode).remove();jQuery('.modalWindowOverlay').hide();});
    elem.find('.button').eq(1).click(function(){jQuery(this.parentNode).remove();jQuery('.modalWindowOverlay').hide();return false;});

    return elem;
  },

  hideById: function()
  {
    this.target.hide()
    if (this.prevTarget[0])
      this.prevTarget.after(this.target);
    else
      this.target.prependTo(this.parentTarget);
    this.hide();
  },

  hide: function()
  {
    if(this.addClass)
    {
      this.window.removeClass(this.addClass);
      this.addClass = null;
    }
    this.window.hide();
    this.windowOverlay.hide();
  },
  keyPressed: function (event)
  {
    if(event.which == 27)
      event.data.that.hide();
  }
});

function buildForIE ()
{
  jQuery('.tabs_block dd, fieldset').prepend("<span class='before'></span>").append("<span class='after'></span>");
}

jQuery(document).ready(function()
{
  if(jQuery.browser.msie)
    buildForIE();
  modalWindow = new ModalWindow('#wrapper');
});