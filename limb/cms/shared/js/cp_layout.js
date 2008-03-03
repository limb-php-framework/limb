var LIMB_WINDOW_DEFAULT_PARAMS = { width: 890, height:500, resizable: true, noautoresize: true };
var ShowFilterDefault = 'Показать фильтр';
var HideFilterDefault = 'Скрыть фильтр';

function control_error()
{
  if(jQuery('.message_error .content ol').css('display')=='none')
    jQuery('.message_error .content ol').slideDown('fast');
  else
    jQuery('.message_error .content ol').slideUp('fast');
}

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
    var filter = jQuery('.filter');
    var filterForm = jQuery('.filter form');
    var list = jQuery('.list');
    if (!filter.is('div'))
      return;

    var activeFilterHTML = '<a class="active_filter"><span>' + this.showFilterStr + '</span></a>';
    var htmlText = '<div class="filter_bottom"><a class="active_filter_bottom"><span>' + this.showFilterStr + '</span></a></div>';

    filter.prepend(activeFilterHTML);
    list.css('margin','0');
    list.after(htmlText);
    this._initBehavior();

    this.activeFilter = jQuery('.filter .active_filter span');
    this.activeFilterBelowList = jQuery('.filter_bottom .active_filter_bottom span');

    if(Limb.cookie(window.location + '.filter') == 1){
      filterForm.show();
      this.activeFilter.text(this.hideFilterStr);
      this.activeFilter.addClass('show');

    }
    else {
      filterForm.hide();
      this.activeFilter.text(this.showFilterStr);
    }

  },

  initActiveFilterClick: function()
  {
    var filterForm = jQuery('.filter form');
    var filterFormBelowList = jQuery('.filter_bottom form');

    if (filterFormBelowList.is('form')){
      filterFormBelowList.hide();
      filterFormBelowList.clone().appendTo(".filter").show('slow');
      filterFormBelowList.remove();
    }
    else
      filterForm.toggle('slow');

    this.setFilterCookie();
    this.initActiveFilter();
  },

  initActiveFilterBelowListClick:function()
  {
    var filterForm = jQuery('.filter form');
    var filterFormBelowList = jQuery('.filter_bottom form');

    if (filterFormBelowList.is('form'))
      filterFormBelowList.toggle('slow');
    else {
      filterForm.hide();
      filterForm.clone().prependTo(".filter_bottom").show('slow');
      filterForm.remove();
    }

    this.setFilterCookie();
    this.initActiveFilter();

  },

  initActiveFilter:function (){
    var filterForm = jQuery('.filter form');

    if (filterForm.is('form')){

        if (this.activeFilter.attr('class')== 'show')
          this.activeFilter.removeClass('show').text(this.showFilterStr);
        else {
          this.activeFilter.addClass('show').text(hideFilterStr);
          this.activeFilterBelowList.removeClass('show').text(this.showFilterStr);
        }
    }
    else{

        if (this.activeFilterBelowList.attr('class')== 'show')
          this.activeFilterBelowList.removeClass('show').text(this.showFilterStr);
        else {
          this.activeFilterBelowList.addClass('show').text(this.hideFilterStr);
          this.activeFilter.removeClass('show').text(this.showFilterStr);
        }

    }
  },

  setFilterCookie: function(){

    if(Limb.cookie(window.location + '.filter') == 1)
      Limb.cookie(window.location + '.filter', 0);
    else
      Limb.cookie(window.location + '.filter', 1);
  },

  _initBehavior: function(){
    jQuery('.active_filter').click(this.initActiveFilterClick.bind(this));
    jQuery('.active_filter_bottom').click(this.initActiveFilterBelowListClick.bind(this));
  }

});

/**
   Resize Image

   Все изображения с атрибутом "resize", ресайзит до размера указанного в значении атрибута "resize".
   Если значение атрибута:
      "xЧисло" то изображение меняет пропорционально свои размеры, где высота равна значению атрибут.
      "Числоx" то изображение меняет пропорционально свои размеры, где ширина равна значению атрибут.
      "Число" то изображение меняет пропорционально свои размеры, в зависимости от того что больше высота или ширина,
      то и принимает значение атрибута.

   TODO: продублировать описание на английском. показать пример использования.
*/


function initImgResize(){
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

    var link = jQuery('<a class="magnifier" href="' + this.src + '" target="_blank"><img src="/shared/cms/images/icon/magnifier.gif" width="13" height="13" alt="magnifier"/></a>').get()[0];

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
};


/**
   Sidebar Toggle

   обрабатывает боковую панель, создаёт для нее манипулятор для скрытия/показа панели.
   запоминает состояние отображения панели (скрыт/доуступен) в cookies

   TODO: продублировать описание на английском. показать пример использования.
*/

Limb.Class('CMS.SidebarToggle',
{
  __construct:function()
  {
     this.sidebar = jQuery('#sidebar');
     this.content = jQuery('#content');
     this.bool = false;


     var SidebarHTML = this.sidebar.html();
     var toggleHTML = '<div class="sidebar_toggle"><span class="text">Навигация</span><span class="arrow"><img src="images/1x1.gif" width="8" height="7" alt="<<" /></span></div>';

     this.sidebar.empty();
     this.sidebar.prepend('<div class="inner"></div>');
     this.inner = jQuery('#sidebar .inner');
     this.inner.prepend(SidebarHTML);

     this.sidebar.prepend(toggleHTML);
     this.toggle = jQuery('.sidebar_toggle');
     this.toggle_text = jQuery('.sidebar_toggle .text');
     this.toggle_arrow = jQuery('.sidebar_toggle .arrow');

     if(Limb.cookie('sidebar_toggle') == 1){
       this.inner.hide();
       this.toggle_text.hide();
       this.hideSidebar();
     }
     this._initBehavior();
  },

  initSidebarStatus: function(){
    if (this.sidebar.attr('class') == 'hide')
       this.bool = true;
     else
       this.bool = false;
     return this.bool;
  },

  showSidebar: function(){
    this.inner.show('slow');
    this.toggle_text.show();
    this.toggle.css('height','');
    this.sidebar.removeClass('hide');
    this.content.css('margin-left','225px');

  },

  hideSidebar: function(){
    this.toggle.height(jQuery('body').height()-53);
    this.toggle_text.hide();
    this.inner.hide('slow');
    this.sidebar.addClass('hide');
    this.sidebar.height(jQuery('body').height()-40);
    this.content.css('margin-left','38px');
  },


  initToggleClick:function(){
    if (this.initSidebarStatus())
      this.showSidebar();
    else
      this.hideSidebar();

    this.setToggleCookie();

    return false;
  },

  setToggleCookie: function(){
    if(Limb.cookie('sidebar_toggle') == 1)
        Limb.cookie('sidebar_toggle', 0);
    else
      Limb.cookie('sidebar_toggle', 1);
  },

  _initBehavior: function(){
    jQuery('.sidebar_toggle .arrow').click(this.initToggleClick.bind(this));
  }

});

/*
  Добавляет свойства к контентой части и боковой панели, благодоря которым
  скроллер для прокрутки доболяется не у документа, а у боковой панели, либо у контента

  TODO: продублировать описание на английском. показать пример использования.
*/

function initDocumentStructure(){
  var container = jQuery('#container');
  var sidebar = jQuery('#sidebar');
  var toggle = jQuery('.sidebar_toggle');
  var text = jQuery('.sidebar_toggle span.text');

  var bodyHeight = jQuery('body').height()-40;
  container.height(bodyHeight);
  sidebar.height(bodyHeight);

  if (text.css('display')=='block')
  return;

  toggle.height (bodyHeight -13);


};

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
    jQuery(link).parent().attr('class', 'current');


  //sliding navigation support
  if(Limb.isFunction(jQuery.fn.accordion_cp))
    jQuery('#main_menu').accordion_cp();

};


/*============================== WINDOW READY ==============================*/
jQuery(window).ready(function(){
    /*SideBar Toggle*/
    new CMS.SidebarToggle('sidebar_toggle');

    initDocumentStructure();
    jQuery(window).resize(initDocumentStructure);

    /*Nice Button*/
    jQuery('.button').wrap('<span class="button_wrapper"></span>');



    // Fiter up/down sliding control
    new CMS.Filter(ShowFilterDefault, HideFilterDefault);

    jQuery('.message_error .show_hidden').bind('click', control_error);

    /*Fix PNG*/
    jQuery('img[@src$=.png], .shadow_bottom span, .shadow_left span, .shadow_right span').ifixpng();

    /*Image Resize*/
    initImgResize();

    //duplicating h1 popup in title
    jQuery('.popup h1').each(
      function()
      {
        document.title = jQuery(this).text();
      }
    );

    /*Main Menu*/
    initMainMenu();

});


