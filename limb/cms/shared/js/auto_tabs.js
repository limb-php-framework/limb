
if(jQuery)
{
  jQuery(window).ready(function()
  {
    autoTabs();
  });

  function autoTabs()
  {
    jQuery('.tabs').each(function(){
      var tabs = [];

      var ul = jQuery(this).children('ul');
      ul.addClass('bookmarks');
      ul.children('li').each(function(){
        if(this.id)
        {
          jQuery('#tab_' + this.id).addClass('bookmarks_content');
          tabs.push(new CMS.Tab(this.id, 'tab_' + this.id));
        }
      });

      if(tabs.length)
        new CMS.TabsContainer(tabs);
    });
  }
}
