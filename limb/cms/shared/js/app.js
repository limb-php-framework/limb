Limb.Class('CMS.Menu',
{
  __construct: function(menu_id, hide_menu_time)
  {
    this.container = document.getElementById(menu_id);
    if(!this.container)
      throw new Limb.Exception('Menu element not found');

    this.submenus = [];
    this.entries_with_submenus = [];
    this.entries_without_submenus = [];
    this.current_submenu = null;
    this._findMenuItems();

    this.showCurrentMenu = false;
    this.hide_timeout = '';
    this.hide_menu_time = hide_menu_time || 500;

    this._initBehavior();
  },

  _findMenuItems: function()
  {
    var links = this.container.getElementsByTagName('a');

    for(var i = 0; i < links.length; i++)
    {
      if(links[i].className != 'menu_entry')
        continue;

      if(this.addSubmenu(links[i]))
        this.entries_with_submenus.push(links[i]);
      else
        this.entries_without_submenus.push(links[i]);
    }
  },

  _initBehavior: function()
  {
    jQuery(this.container).bind('mouseover', this.onMouseOver.bind(this));
    jQuery(this.container).bind('mouseout', this.onMouseOut.bind(this));

    for(var i = 0; i < this.entries_with_submenus.length; i++)
      jQuery(this.entries_with_submenus[i]).bind('mouseover', this.showSubMenu.bind(this, [ this.entries_with_submenus[i],this.submenus[i] ]));

    for(var i = 0; i < this.entries_without_submenus.length; i++)
      jQuery(this.entries_without_submenus[i]).bind('mouseover', this.hideAllNow.bind(this));

    for(var i = 0; i < this.entries_without_submenus.length; i++)
      jQuery(this.entries_without_submenus[i]).bind('mouseover', this.setBehaviour.bind(this, [ this.entries_without_submenus[i] ] ));

    this.showDefaultMenu();
  },

  addSubmenu: function(menu_entry)
  {
    if(!menu_entry)
      return false;

    var submenu = menu_entry.nextSibling;
    while(submenu && (!submenu.tagName || submenu.tagName.toLowerCase() != 'div'))
      submenu = submenu.nextSibling;

    if(!submenu)
      return false;

    this.submenus.push(submenu);

    if(jQuery(menu_entry).attr('id') == 'current_menu')
     this.current_submenu = submenu;

    return true;
  },

  showSubMenu: function( link , menu)
  {
    if(!menu)
      return;
    this.showCurrentMenu = true;
    this.hideAllNow();
    this.setBehaviour (link);
    var header_m = document.getElementById('header_menu');
    var virtual_submenu = document.createElement('div');
    virtual_submenu.id = 'virtual_submenu';
    virtual_submenu.innerHTML  = menu.innerHTML;
    header_m.appendChild(virtual_submenu);

  },

  hideMenu: function(menu)
  {
    menu.style.display = 'none';
  },

  hideAll: function(force_hide)
  {
    if(!force_hide && this.showCurrentMenu)
      return;

    for(i = 0; i < this.submenus.length; i ++)
      this.hideMenu(this.submenus[i]);
    header_m = document.getElementById("header_menu");
    virtual_submenu = document.getElementById("virtual_submenu");
    if (!virtual_submenu)
      return;
    header_m.removeChild(virtual_submenu);
  },

  hideAllNow: function()
  {
    this.hideAll(true);
  },

  onMouseOut: function()
  {
    this.showCurrentMenu = false;
    this.hide_timeout = setTimeout(this.showDefaultMenu.bind(this), this.hide_menu_time);
  },

  onMouseOver: function()
  {
    if(!this.hide_timeout)
      return;

    this.showCurrentMenu = true;
    this._abortHide();
  },

  showDefaultMenu: function()
  {
    if(!this.current_submenu)
      {
        this.hideAllNow();
        this.returnBehaviour();
        return;
      }
    this.showSubMenu('', this.current_submenu);
    this.returnBehaviour();
  },

  setBehaviour: function(link_hover)
  {
   if(!link_hover)
      return;
   this.returnBehaviour();
   link_hover.className = 'current_link';
   this.Z = link_hover.style.zIndex;
   jQuery("#header_menu a.current_link").css("zIndex", '2000');
   jQuery("#header_menu a.current_link").css("background", 'url(/images/menu/menu_current.gif) no-repeat right top');
   jQuery("#header_menu a.current_link").css("color", '#fff');
  },

  returnBehaviour: function()
  {
   if(jQuery("#header_menu a.current_link").attr('id')!= 'current_menu')
   {
    jQuery("#header_menu a.current_link").css("zIndex", this.Z);
    jQuery("#header_menu a.current_link").css("background", 'url(/images/menu/menu.gif) no-repeat right top');
    jQuery("#header_menu a.current_link").css("color", '#000');
   }
   jQuery("#header_menu a.current_link").attr('class','menu_entry');
  },

  _abortHide: function()
  {
    if(!this.hide_timeout)
      return;

    clearTimeout(this.hide_timeout);
    this.hide_timeout = null;
  }
});


function control_form_forum()
{
  if (jQuery('.post_comment').next().attr('id') != 'forum_message_form')
    return;
  if(jQuery('.post_comment').next().css('display')=='none')
    jQuery('.post_comment').next().slideDown('fast');
  else
    jQuery('.post_comment').next().slideUp('fast');
}


function toggle_post (elem_selector, url){
  var toggle = jQuery(elem_selector).toggle();
  if (jQuery(toggle).css('display') != 'none'){
    if (jQuery(elem_selector).html() == '&nbsp;')
      jQuery(elem_selector).load(url);
  }
}

function toggle_show (elem_selector, url, id){
  var toggle = jQuery(elem_selector).toggle();
  var first_link = jQuery ('#first_view_comments_'+id);
  var second_link = jQuery ('#second_view_comments_'+id);
  if (jQuery(toggle).css('display') != 'none'){
    if (jQuery(elem_selector).html() == '&nbsp;')
      jQuery(elem_selector).load(url);
    first_link.find('span').html('Hide comments:');
   second_link.show();
  }
  else {
    second_link.hide();
    first_link.find('span').html('Show comments:');
  }
}

//do some actions on window load
jQuery(window).ready(function(){

  jQuery('.post_comment').bind('click', control_form_forum);

  var url = window.location.toString()
  var max = 0;
  var link = null;

  //draw left menu
  jQuery("#sidemenu > ul > li > a").each(function(){
    if(url.indexOf(this.href) >= 0)
      this.className = 'active';
  });

  jQuery("#header_menu .submenu a").each(function(){
    if(url.indexOf(this.href) >= 0 && this.href.length > max)
    {
      link = this;
      max = this.href.length;
    }
  });
   if(link){
      jQuery(link).css('font','bold 11px Verdana')
      jQuery(link).parents(".submenu").prev().attr('id','current_menu');
      jQuery("#current_menu").css('zIndex','3000');
    }
    else
      jQuery("#header_menu a.menu_entry").each(function(){
        if(url.indexOf(jQuery(this).attr('href')) >= 0)
        {
          jQuery(this).attr('id','current_menu');
          jQuery("#current_menu").css('zIndex','3000');
        }
      });
  if(window.location.pathname == '/')
    jQuery(".menu_entry").get(0).id = 'current_menu';
  //draw header menu
    new CMS.Menu('header_menu');
});
