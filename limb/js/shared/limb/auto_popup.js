jQuery(window).ready(
  function()
  {
   jQuery("a").each(
    function()
    {
      if(this.href.indexOf('popup=1') > -1)
      {
        jQuery(this).bind("click", function()
                       {
                         new Limb.Window(this.href);
                         return false;
                       });
      }
    }
   );
  }
);