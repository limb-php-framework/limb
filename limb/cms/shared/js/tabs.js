Limb.namespace('CMS');

Limb.Class('CMS.TabsContainer',
{
  __construct: function(tabs)
  {
    this.tabs = [];

    if(tabs)
    {
      this.tabs = tabs;

      for(var i=0;i<this.tabs.length;i++)
      {
        this.tabs[i].setIndex(i);
        this.tabs[i].setContainer(this);
      }

      this.activateTab(0);
    }
  },

  activateTab: function(index)
  {
    if(!this.tabs[index])
      return;

    this.deactivateAll();
    this.tabs[index].activate();
  },

  addTab: function(tab)
  {
    tab.setIndex(this.tabs.length);
    tab.setContainer(this);
    this.tabs.push(tab);
  },

  deactivateAll: function()
  {
    for(var i=0;i<this.tabs.length;i++)
      this.tabs[i].deactivate();
  }

});

Limb.Class('CMS.Tab',
{
  __construct: function(link_id, content_id)
  {
    this.link = document.getElementById(link_id);
    this.content = document.getElementById(content_id);
    this.container = null;
    this.index = -1;

    this._initBehavior();
  },

  setIndex: function(index)
  {
    this.index = index;
  },

  setContainer: function(tabs_container)
  {
    this.container = tabs_container;
  },

  activate: function()
  {
   this.link.className = 'active';
   if (Limb.Browser.is_win_ie)
      jQuery(this.content).css('display','block');
    else
      jQuery(this.content).css({
                          visibility:'visible',
                          minHeight: '320px',
                          maxHeight: '10000',
                          padding: '10px 10px 1px',
                          border:'1px solid #ddd',
                          borderTop:'3px solid #7d7d7d',
                          borderBottom: '0'
                          })

  },

  deactivate: function()
  {
    this.link.className = '';
    if (Limb.Browser.is_win_ie)
      jQuery(this.content).css('display','none');
    else
      jQuery(this.content).css({
                          visibility:'hidden',
                          minHeight: '0',
                          maxHeight: '0',
                          padding: '0',
                          border:'0'
                          })

  },

  onActivate: function()
  {
    if(this.index == -1 || !this.container)
      return;

    this.container.activateTab(this.index);
  },

  _initBehavior: function()
  {
    this.link.onclick = this.onActivate.bind(this);
  }
});