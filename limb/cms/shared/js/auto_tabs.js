
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
        var oldID = this.id;
        this.id = 'bookmark-' + this.id;
        if(oldID)
        {
          jQuery('#tab_' + oldID).addClass('bookmarks_content');
          tabs.push(new CMS.Tab(this.id, 'tab_' + oldID));
        }
      });

      if(tabs.length)
        window.cmsTabContainer = new CMS.TabsContainer(tabs);
    });
  }
}
