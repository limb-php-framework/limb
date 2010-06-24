var ShowFilterDefault = 'Показать фильтр';
var HideFilterDefault = 'Скрыть фильтр';

/////// LAYOUT POSTPROCESSING HELPERS

/**
   Filter formatting

   обрабатывает форму фильтра поиска при выводе списков в панели управления
   позволяет скрывать/показывать фильтр. при необходимости отображает форму поиска в конце списка.
   запоминает состояние отображения (скрыт/доуступен) конкретного фильтра в cookies

   TODO: продублировать описание на английском. показать пример использования.
*/
Limb.Class('CMS.Filter',
{
  __construct:function(showFilterStr, hideFilterStr)
  {
    this.showFilterStr = showFilterStr || ShowFilterDefault || '';
    this.hideFilterStr = hideFilterStr || HideFilterDefault || '';

    this.mainFilter = jQuery('.filter');
    this.mainFilterForm = this.mainFilter.find('form');
    if (!this.mainFilter[0])
      return;

    this.list = this.mainFilter.next('.list').css('margin-bottom','0');

    var togllerFilterHTML = '<a class="toggler closingToggler" href="javascript:void(0)"><span>' + this.hideFilterStr + '</span></a>';
    var bottomFilterHTML = '<div class="filter cloned_filter closedFilter"><a class="toggler openingToggler" href="javascript:void(0)"><span>' + this.showFilterStr + '</span></a></div>';

    this.mainFilter.prepend(togllerFilterHTML);
    this.list.after(bottomFilterHTML);

    this.cloneFilter = this.list.next('.filter');
    this.cloneFilterForm = this.cloneFilter.find('form');

    this.mainToggler = this.mainFilter.find('.toggler');
    this.cloneToggler = this.cloneFilter.find('.toggler');

    this._initBehavior();

    if(!cookieCMS.get(window.location + '.filter'))
      this.toggleFilter(this.mainToggler);
  },

  openFilter: function(toggler,obj)
  {
    var toggler = jQuery(toggler);
    var cloneToggler = null;
    var mainToggler = null;
    var filter = toggler.parent('.filter');

    if (filter.next().hasClass('list'))
    {
     cloneToggler = filter.next().next('.filter').find('.toggler');
     mainToggler = toggler;
    }
    if (filter.prev().hasClass('list'))
    {
     mainToggler = filter.prev().prev('.filter').find('.toggler');
     cloneToggler = toggler;
    }

    this.toggleFilter(toggler);

    this.setFilterCookie();

    var form = mainToggler.next('form')[0] ||  cloneToggler.next('form')[0];

    if (!form)
      return;

    form = jQuery(form);

    if (!toggler.next('form')[0])
    {
      if (!form.parent('.filter').hasClass('closedFilter'))
        this.toggleFilter(form.prev('.toggler'));
      form.appendTo(filter);
    }

  },

  toggleFilter: function(toggler)
  {
    var filter = toggler.parent('.filter');
    if (filter.hasClass('closedFilter'))
    {
      filter.removeClass('closedFilter');
      toggler.addClass('closingToggler').removeClass('openingToggler').children().text(this.hideFilterStr);
    }
    else
    {
      filter.addClass('closedFilter');
      toggler.addClass('openingToggler').removeClass('closingToggler').children().text(this.showFilterStr)
    }
  },

  setFilterCookie: function(){

    if(cookieCMS.get(window.location + '.filter'))
      cookieCMS.remove(window.location + '.filter');
    else
      cookieCMS.set(window.location + '.filter');
  },

  _initBehavior: function(){
    var obj = this;
    this.mainToggler.click(function(){obj.openFilter(this,obj)});
    this.cloneToggler.click(function(){obj.openFilter(this,obj)})
  }

});

jQuery.fn.accordion_cp = function(options) {
    // options
    var SLIDE_DOWN_SPEED = 'fast';
    var SLIDE_UP_SPEED = 'fast';
    var startClosed = options && options.start && options.start == 'closed';
    var on = options && options.on && (typeof options.on == 'number' && options.on > 0) ? options.on - 1 : 0;
    return this.each(function() {
        jQuery(this).addClass('accordion'); // use to activate styling
        jQuery(this).find('dd').hide();

        jQuery(this).find('dt').click(function() {
            var current = jQuery(this.parentNode).find('dd:visible');
            var next = jQuery(this).find('+dd');
            if(next[0] == current[0])
              return;

            if (current[0] != next[0]) {
                current.slideUp(SLIDE_UP_SPEED);
            }
            if (next.is(':visible')) {
                next.slideUp(SLIDE_UP_SPEED);
            } else {
                next.slideDown(SLIDE_DOWN_SPEED);
            }
        });
        if (!startClosed) {
            var elem = jQuery(this).find("dd > ul > li.current").parents('dd');
            if(!elem.get()[0])
            {
              jQuery(this).find('dd:first').slideDown(SLIDE_DOWN_SPEED);
              return;
            }
            elem.slideDown(SLIDE_DOWN_SPEED);
        }
    });
};

Limb.Class('CMS.SidebarToggler',
{
  __construct:function()
  {
     this.sidebar = jQuery('#sidebar');
     this.content = jQuery('#main_col');
     this.bool = false;

     var togglerHTML = '<div class="sidebar_toggler"><span class="text">Навигация</span><span class="arrow">«</span></div>';
     this.sidebar.prepend(togglerHTML);

     this.toggler = this.sidebar.find('.sidebar_toggler');

     if(cookieCMS.get('hiddenLimbSidebar'))
       this.toggleSidebar(this);

     this._initBehavior();
  },

  toggleSidebar:function(object){
    var wrapper = jQuery('#wrapper');
    if (jQuery('#wrapper').hasClass('hiddenSidebar'))
    {
      wrapper.removeClass('hiddenSidebar');
      this.toggler.find('.arrow').text('«');
      cookieCMS.remove('hiddenLimbSidebar');
      if(jQuery.browser.msie)
        this.toggler.height('auto');
    }
    else
    {
      wrapper.addClass('hiddenSidebar');
      this.toggler.find('.arrow').text('»');
      cookieCMS.set('hiddenLimbSidebar', 1);
      if(jQuery.browser.msie)
        this.toggler.height(wrapper.height()-jQuery('#admin_panel').height()-15);
    }
  },

  _initBehavior: function(){
    var object = this;
    jQuery('.sidebar_toggler').click(function(){object.toggleSidebar(object);return false;});
  }

});

function initMainMenu(){
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
  {
    jQuery(link).parent().attr('class', 'current');
    jQuery(link).parents('dd').prev().addClass('current');
  }


  //sliding navigation support
  if(Limb.isFunction(jQuery.fn.accordion_cp))
    jQuery('#main_menu').accordion_cp();

};


Limb.Class('CMS.lightLimbVersion',
{
  light_enabled_text: "лайт-версия. выключить.",
  light_enabled_title: "Выключить лайт-версию дизайна. Будут работать дополнительные интерфейсные элементы. Будет удобнее, но дольше.",
  light_disabled_text: "лайт-версия. включить.",
  light_disabled_title: "Включить лайт-версию дизайна. Пзволяет отключить интерфейсный javascript для ускорения загрузки",

  __construct:function()
  {
    if(cookieCMS.get('lightLimbVersion'))
      jQuery('#admin_panel #user_data').before("<a href='javascript:void(0)' id='light_version' class='enabled' title='" + this.light_enabled_title + "'>" + this.light_enabled_text + "</a>");
    else
      jQuery('#admin_panel #user_data').before("<a href='javascript:void(0)' id='light_version' title='" + this.light_disabled_title + "'>" + this.light_disabled_text + "</a>");

    this.link = jQuery('#light_version');

    this._initBehavior();
  },
  toggle: function()
  {
    if(cookieCMS.get('lightLimbVersion'))
    {
      this.link.text(this.light_disabled_text).removeClass('enabled').attr('title', this.light_enabled_title);
      cookieCMS.remove('lightLimbVersion');
    }
    else
    {
      this.link.text(this.light_disabled_text).addClass('enabled').attr('title', this.light_disabled_title);
      cookieCMS.set('lightLimbVersion');
    }
    window.location.reload();//плохо, потому что при POST запросе выпадет диалог с уточнением перезагрузки
  },
  _initBehavior: function()
  {
    var obj = this;
    this.link.click(function(){obj.toggle();return false;})
  }
});

Limb.Class('CMS.cookie',
{
  __construct:function(CMScookieName)
  {
    this.CMScookieName = CMScookieName || "LimbCMS";
    if (!Limb.cookie(this.CMScookieName))
      Limb.cookie(this.CMScookieName, ";");
  },
  get: function(cookieName)
  {
    var cookie = Limb.cookie(this.CMScookieName);
    if(!cookie)
      return;
    cookieName = ";" + cookieName + ";";
    if(cookie.match(cookieName))
      return true;
    else
      return false;
  },
  set: function(cookieName)
  {
    var cookie = Limb.cookie(this.CMScookieName);
    if(!cookie.match(";" + cookieName + ";"))
      Limb.cookie(this.CMScookieName, cookie + cookieName+';');
  },
  remove: function(cookieName)
  {
    var cookie = Limb.cookie(this.CMScookieName);
    cookie = cookie.replace(cookieName + ";", "");
    Limb.cookie(this.CMScookieName, cookie);
  }
});

/*
  Добавляет свойства к контентой части и боковой панели, благодоря которым
  скроллер для прокрутки доболяется не у документа, а у боковой панели, либо у контента

  TODO: продублировать описание на английском. показать пример использования.
*/

function initDocumentStructure(){
  var main_col = jQuery('#main_col');
  var sidebar = jQuery('#sidebar');

  var height = jQuery('#wrapper').height()-jQuery('#admin_panel').height();
  window.status = jQuery('#wrapper').height();
  main_col.height(height).css('overflow','auto');
  sidebar.height(height);
};

function setFocusOnPopupWindow()
{
  var popup_window = document.getElementById('TB_iframeContent');
  if(!popup_window)
    return;

  var firstElement  = jQuery(popup_window.contentWindow.document).find('a, :input')[0];
  if(firstElement)
    firstElement.focus();
}


function toggle_selected(toggle_obj)
{
  var parent = toggle_obj.form || jQuery(toggle_obj).parents('table');
  var mark = toggle_obj.checked;
  jQuery("input:checkbox[@name='ids[]']", parent).each(function(){jQuery(this).attr("checked", mark);});
}


/*============================== WINDOW READY ==============================*/
jQuery(window).ready(function(){

  /*Keep focus into popup window*/
  /*window.onfocus = setFocusOnPopupWindow;
  document.onfocus = setFocusOnPopupWindow;
  window.onkeydown = setFocusOnPopupWindow;
  document.onkeydown = setFocusOnPopupWindow
  */

  cookieCMS = new CMS.cookie('LimbCMS');
  new CMS.lightLimbVersion();

  if(cookieCMS.get('lightLimbVersion'))
  {
    jQuery.fx.off = true;
    return false;
  }

  var currheight;
  if(!jQuery.browser.msie)
  {
    initDocumentStructure();
    jQuery(window).resize(initDocumentStructure);
  }

  /*Nice Button*/
  jQuery('input[type=button], input[type=submit]').wrap('<span class="button_wrapper"></span>');

  /*SideBar Toggle*/
  new CMS.SidebarToggler();

  // Fiter up/down sliding control
  new CMS.Filter(ShowFilterDefault, HideFilterDefault);

  /*Main Menu*/
  initMainMenu();

});


