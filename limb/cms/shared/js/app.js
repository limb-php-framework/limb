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
